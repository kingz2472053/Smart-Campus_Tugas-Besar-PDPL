# 🎬 Script Presentasi Video — Teofilus Juan Puapadang

> **Durasi target:** ±2 menit 30 detik  
> Teks dalam *[kurung siku italic]* = instruksi tampilan layar, **tidak dibacakan**.

---

## PEMBUKAAN *(~10 detik)*

*[Tampilkan halaman utama aplikasi Smart Campus]*

> "Selamat pagi/siang. Saya **Teofilus Juan Puapadang**, bertanggung jawab atas tiga fitur: **Manajemen Tugas CRUD**, **Undo/Redo**, dan **Notifikasi MultiChannel**."

---

## FITUR 1 — Manajemen Tugas / CRUD *(~50 detik)*

### Design Pattern: **Command Pattern**

*[Tampilkan demo di browser: buat tugas → edit → hapus]*

> "Fitur pertama adalah CRUD Tugas. Dosen bisa membuat, mengedit, dan menghapus tugas. Saya menggunakan **Command Pattern** — setiap operasi CRUD dibungkus sebagai objek command yang terpisah."

> "Strukturnya ada empat bagian: **Interface, Concrete Command, Invoker, dan Client**. Berikut kodenya:"

---

*[Tampilkan snippet 1]*

📁 **File:** [TaskCommandInterface.php](file:///c:/Semester%204/PDPL/Tubes/Smart-Campus_Tugas-Besar-PDPL/SmartCampus/app/Contracts/TaskCommandInterface.php) — Baris 17–56 *(Interface Command)*

```php
interface TaskCommandInterface
{
    public function execute(): mixed;       // Jalankan operasi
    public function getAction(): string;    // Nama aksi untuk logging
    public function getDetail(): array;     // Data audit trail (untuk undo/redo)
    public function getTargetTable(): string;
    public function getTargetId(): ?int;
}
```

> "Ini **interface-nya** — kontrak yang wajib diikuti semua command."

---

*[Tampilkan snippet 2]*

📁 **File:** [CreateTaskCommand.php](file:///c:/Semester%204/PDPL/Tubes/Smart-Campus_Tugas-Besar-PDPL/SmartCampus/app/Services/Task/CreateTaskCommand.php) — Baris 20–71 *(Concrete Command)*

```php
class CreateTaskCommand implements TaskCommandInterface
{
    private ?Assignment $createdAssignment = null;

    public function __construct(private readonly array $data) {}

    public function execute(): mixed
    {
        $this->createdAssignment = Assignment::create($this->data);
        // ... otomatis kirim notifikasi ke mahasiswa
        return $this->createdAssignment;
    }

    public function getAction(): string { return 'CREATE_ASSIGNMENT'; }

    public function getDetail(): array { return ['created' => $this->data]; }
}
```

> "Ini salah satu **Concrete Command**. Ada tiga: `CreateTaskCommand`, `EditTaskCommand`, dan `DeleteTaskCommand` — masing-masing mengenkapsulasi satu operasi."

> File lainnya:
> - [EditTaskCommand.php](file:///c:/Semester%204/PDPL/Tubes/Smart-Campus_Tugas-Besar-PDPL/SmartCampus/app/Services/Task/EditTaskCommand.php) — Baris 20–58
> - [DeleteTaskCommand.php](file:///c:/Semester%204/PDPL/Tubes/Smart-Campus_Tugas-Besar-PDPL/SmartCampus/app/Services/Task/DeleteTaskCommand.php) — Baris 20–55

---

*[Tampilkan snippet 3]*

📁 **File:** [TaskCommandInvoker.php](file:///c:/Semester%204/PDPL/Tubes/Smart-Campus_Tugas-Besar-PDPL/SmartCampus/app/Services/Task/TaskCommandInvoker.php) — Baris 29–56 *(Invoker)*

```php
class TaskCommandInvoker
{
    public function execute(TaskCommandInterface $command, int $userId): mixed
    {
        $result = $command->execute();                    // Jalankan command

        $log = ActivityLogger::getInstance()->log(        // Logging otomatis
            action: $command->getAction(),
            userId: $userId,
            targetTable: $command->getTargetTable(),
            targetId: $command->getTargetId(),
            detail: $command->getDetail()
        );

        $this->pushToUndoStack($log->id);                // Simpan ke undo stack
        Session::forget('task_redo_stack');                // Reset redo stack

        return $result;
    }
}
```

> "**Invoker** menjalankan command apa pun, otomatis mencatat log, dan mengelola undo stack."

---

*[Tampilkan snippet 4]*

📁 **File:** [AssignmentController.php](file:///c:/Semester%204/PDPL/Tubes/Smart-Campus_Tugas-Besar-PDPL/SmartCampus/app/Http/Controllers/AssignmentController.php) — Baris 152–166 *(Client)*

```php
// store() — Client membuat command, kirim ke Invoker
public function store(Request $request)
{
    $validated = $this->validateAssignment($request);
    $validated['created_by'] = Auth::id();

    $command = new CreateTaskCommand($validated);               // Buat command
    $assignment = $this->invoker->execute($command, Auth::id()); // Kirim ke Invoker

    return redirect()->route('dosen.assignments.show', $assignment)
        ->with('success', 'Tugas berhasil dibuat.');
}
```

> "Di **Controller sebagai Client**, kita cukup buat command lalu kirim ke Invoker. Controller tidak perlu tahu detail penyimpanan atau logging."

---

## FITUR 2 — Undo / Redo *(~30 detik)*

### Design Pattern: **Command Pattern + Memento-like approach**

*[Tampilkan demo: hapus tugas → klik Undo → tugas kembali]*

> "Fitur kedua, **Undo/Redo**, terintegrasi langsung dengan Command Pattern tadi. Setiap operasi menyimpan *snapshot* data sebelum perubahan — ini konsep Memento. Dosen bisa membatalkan atau mengulangi aksi terakhirnya."

---

*[Tampilkan snippet 5]*

📁 **File:** [TaskCommandInvoker.php](file:///c:/Semester%204/PDPL/Tubes/Smart-Campus_Tugas-Besar-PDPL/SmartCampus/app/Services/Task/TaskCommandInvoker.php) — Baris 77–103 *(Method undo)*

```php
public function undo(int $userId): ?string
{
    $undoStack = Session::get('task_undo_stack', []);
    if (empty($undoStack)) return null;

    $logId = array_pop($undoStack);                // Ambil aksi terakhir
    Session::put('task_undo_stack', $undoStack);

    $log = ActivityLog::find($logId);
    if (!$log || $log->user_id !== $userId) return null;

    $message = $this->performUndo($log);           // Batalkan operasinya

    if ($message) {
        $redoStack = Session::get('task_redo_stack', []);
        array_push($redoStack, $logId);            // Pindahkan ke redo stack
        Session::put('task_redo_stack', $redoStack);
    }
    return $message;
}
```

> "Method `undo()` mengambil aksi terakhir dari stack, membatalkannya, lalu memindahkan ke redo stack."

---

*[Tampilkan snippet 6]*

📁 **File:** [TaskCommandInvoker.php](file:///c:/Semester%204/PDPL/Tubes/Smart-Campus_Tugas-Besar-PDPL/SmartCampus/app/Services/Task/TaskCommandInvoker.php) — Baris 139–205 *(Method performUndo)*

```php
private function performUndo(ActivityLog $log): ?string
{
    switch ($log->action) {
        case 'CREATE_ASSIGNMENT':
            // Undo create = hapus tugas yang baru dibuat
            $assignment = Assignment::find($targetId);
            $assignment->delete();
            return "Pembuatan tugas dibatalkan (Undo).";

        case 'UPDATE_ASSIGNMENT':
            // Undo edit = kembalikan ke data sebelumnya
            $assignment->update($detail['before']);
            return "Pembaruan tugas dibatalkan (Undo).";

        case 'DELETE_ASSIGNMENT':
            // Undo delete = restore tugas + submissions + grades dari snapshot
            $assignment = new Assignment($deletedData);
            $assignment->save();
            // ... restore submissions & grades
            return "Penghapusan tugas dibatalkan (Undo).";
    }
}
```

> "Di `performUndo`, setiap jenis aksi punya cara pembatalannya sendiri. Yang paling kompleks adalah undo delete — tugas beserta submissions dan nilainya di-restore dari snapshot."

---

## FITUR 3 — Notifikasi MultiChannel *(~40 detik)*

### Design Pattern: **Factory Method Pattern**

*[Tampilkan icon lonceng di topbar → klik → dropdown notifikasi]*

> "Fitur ketiga, **Notifikasi MultiChannel**. Saat dosen membuat tugas baru, notifikasi otomatis terkirim ke mahasiswa lewat **Dashboard** atau **Email**. Saya menggunakan **Factory Method Pattern** dengan struktur: **Interface Product, Concrete Product, Abstract Creator, dan Concrete Creator**."

---

*[Tampilkan snippet 7]*

📁 **File:** [NotificationChannelInterface.php](file:///c:/Semester%204/PDPL/Tubes/Smart-Campus_Tugas-Besar-PDPL/SmartCampus/app/Contracts/NotificationChannelInterface.php) — Baris 7–18 *(Interface Product)*

```php
interface NotificationChannelInterface
{
    public function send(User $user, string $message, array $payload = []): bool;
}
```

> "**Interface Product** — setiap channel wajib punya method `send()`."

---

*[Tampilkan snippet 8]*

📁 **File:** [DashboardChannel.php](file:///c:/Semester%204/PDPL/Tubes/Smart-Campus_Tugas-Besar-PDPL/SmartCampus/app/Services/Notification/DashboardChannel.php) — Baris 10–31  
📁 **File:** [EmailChannel.php](file:///c:/Semester%204/PDPL/Tubes/Smart-Campus_Tugas-Besar-PDPL/SmartCampus/app/Services/Notification/EmailChannel.php) — Baris 12–37  
*(Concrete Products)*

```php
// DashboardChannel — simpan notifikasi ke database (tampil di lonceng)
class DashboardChannel implements NotificationChannelInterface
{
    public function send(User $user, string $message, array $payload = []): bool
    {
        Notification::create([
            'user_id' => $user->id, 'channel' => 'dashboard',
            'message' => $message, 'is_read' => false, 'sent_at' => now(),
        ]);
        return true;
    }
}

// EmailChannel — kirim email riil via Laravel Mail
class EmailChannel implements NotificationChannelInterface
{
    public function send(User $user, string $message, array $payload = []): bool
    {
        Mail::to($user->email)->send(new NotificationMail($user, $message));
        return true;
    }
}
```

> "Dua **Concrete Product**: `DashboardChannel` menyimpan ke database, `EmailChannel` mengirim email riil."

---

*[Tampilkan snippet 9]*

📁 **File:** [NotificationSender.php](file:///c:/Semester%204/PDPL/Tubes/Smart-Campus_Tugas-Besar-PDPL/SmartCampus/app/Services/Notification/NotificationSender.php) — Baris 8–30 *(Abstract Creator)*  
📁 **File:** [DashboardNotificationSender.php](file:///c:/Semester%204/PDPL/Tubes/Smart-Campus_Tugas-Besar-PDPL/SmartCampus/app/Services/Notification/DashboardNotificationSender.php) — Baris 7–13 *(Concrete Creator)*  
📁 **File:** [EmailNotificationSender.php](file:///c:/Semester%204/PDPL/Tubes/Smart-Campus_Tugas-Besar-PDPL/SmartCampus/app/Services/Notification/EmailNotificationSender.php) — Baris 7–13 *(Concrete Creator)*

```php
// Abstract Creator — Factory Method
abstract class NotificationSender
{
    abstract protected function createChannel(): NotificationChannelInterface; // Factory Method

    public function sendNotification(User $user, string $message, array $payload = []): bool
    {
        $channel = $this->createChannel();      // Subclass menentukan channel-nya
        return $channel->send($user, $message, $payload);
    }
}

// Concrete Creator
class DashboardNotificationSender extends NotificationSender
{
    protected function createChannel(): NotificationChannelInterface
    {
        return new DashboardChannel();
    }
}

class EmailNotificationSender extends NotificationSender
{
    protected function createChannel(): NotificationChannelInterface
    {
        return new EmailChannel();
    }
}
```

> "**Abstract Creator** mendefinisikan Factory Method `createChannel()`. Setiap **Concrete Creator** menentukan channel mana yang dibuat. Kalau mau tambah channel baru seperti WhatsApp, tinggal buat class baru — kode lama tidak perlu diubah. Ini prinsip **Open/Closed**."

---

## PENUTUP *(~10 detik)*

*[Tampilkan halaman utama]*

> "Demikian tiga fitur yang saya kerjakan: **CRUD Tugas** dengan Command Pattern, **Undo/Redo** dengan Command dan Memento, serta **Notifikasi MultiChannel** dengan Factory Method. Terima kasih."

---

## 📁 Ringkasan File & Code Snippet

| No | Fitur | File | Baris | Peran |
|----|-------|------|-------|-------|
| 1 | CRUD | [TaskCommandInterface.php](file:///c:/Semester%204/PDPL/Tubes/Smart-Campus_Tugas-Besar-PDPL/SmartCampus/app/Contracts/TaskCommandInterface.php) | 17–56 | Interface Command |
| 2 | CRUD | [CreateTaskCommand.php](file:///c:/Semester%204/PDPL/Tubes/Smart-Campus_Tugas-Besar-PDPL/SmartCampus/app/Services/Task/CreateTaskCommand.php) | 20–71 | Concrete Command (Create) |
| 3 | CRUD | [EditTaskCommand.php](file:///c:/Semester%204/PDPL/Tubes/Smart-Campus_Tugas-Besar-PDPL/SmartCampus/app/Services/Task/EditTaskCommand.php) | 20–58 | Concrete Command (Edit) |
| 4 | CRUD | [DeleteTaskCommand.php](file:///c:/Semester%204/PDPL/Tubes/Smart-Campus_Tugas-Besar-PDPL/SmartCampus/app/Services/Task/DeleteTaskCommand.php) | 20–55 | Concrete Command (Delete) |
| 5 | CRUD + Undo | [TaskCommandInvoker.php](file:///c:/Semester%204/PDPL/Tubes/Smart-Campus_Tugas-Besar-PDPL/SmartCampus/app/Services/Task/TaskCommandInvoker.php) | 29–56 | Invoker |
| 6 | CRUD | [AssignmentController.php](file:///c:/Semester%204/PDPL/Tubes/Smart-Campus_Tugas-Besar-PDPL/SmartCampus/app/Http/Controllers/AssignmentController.php) | 152–166 | Client (Controller) |
| 7 | Undo/Redo | [TaskCommandInvoker.php](file:///c:/Semester%204/PDPL/Tubes/Smart-Campus_Tugas-Besar-PDPL/SmartCampus/app/Services/Task/TaskCommandInvoker.php) | 77–103 | Undo method |
| 8 | Undo/Redo | [TaskCommandInvoker.php](file:///c:/Semester%204/PDPL/Tubes/Smart-Campus_Tugas-Besar-PDPL/SmartCampus/app/Services/Task/TaskCommandInvoker.php) | 139–205 | performUndo (restore) |
| 9 | Notifikasi | [NotificationChannelInterface.php](file:///c:/Semester%204/PDPL/Tubes/Smart-Campus_Tugas-Besar-PDPL/SmartCampus/app/Contracts/NotificationChannelInterface.php) | 7–18 | Interface Product |
| 10 | Notifikasi | [DashboardChannel.php](file:///c:/Semester%204/PDPL/Tubes/Smart-Campus_Tugas-Besar-PDPL/SmartCampus/app/Services/Notification/DashboardChannel.php) | 10–31 | Concrete Product |
| 11 | Notifikasi | [EmailChannel.php](file:///c:/Semester%204/PDPL/Tubes/Smart-Campus_Tugas-Besar-PDPL/SmartCampus/app/Services/Notification/EmailChannel.php) | 12–37 | Concrete Product |
| 12 | Notifikasi | [NotificationSender.php](file:///c:/Semester%204/PDPL/Tubes/Smart-Campus_Tugas-Besar-PDPL/SmartCampus/app/Services/Notification/NotificationSender.php) | 8–30 | Abstract Creator |
| 13 | Notifikasi | [DashboardNotificationSender.php](file:///c:/Semester%204/PDPL/Tubes/Smart-Campus_Tugas-Besar-PDPL/SmartCampus/app/Services/Notification/DashboardNotificationSender.php) | 7–13 | Concrete Creator |
| 14 | Notifikasi | [EmailNotificationSender.php](file:///c:/Semester%204/PDPL/Tubes/Smart-Campus_Tugas-Besar-PDPL/SmartCampus/app/Services/Notification/EmailNotificationSender.php) | 7–13 | Concrete Creator |
