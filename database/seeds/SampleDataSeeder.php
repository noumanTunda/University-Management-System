<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Student;

/**
 * Seed minimal sample data for every table.
 * This helps avoid "null" related errors when the dashboard expects data.
 * Foreign key checks are temporarily disabled to allow inserting rows in any order.
 */
class SampleDataSeeder extends Seeder
{
    public function run()
    {
        // Disable foreign key checks for the duration of this seeder.
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // users (already seeded via UserTableSeeder/AdminUserSeeder, but ensure at least one generic user)
        DB::table('users')->insert([
            'firstname' => 'John',
            'lastname'  => 'Doe',
            'description'=> null,
            'login'     => 'johndoe',
            'email'     => 'john@example.com',
            'group'     => 'user',
            'password'  => bcrypt('secret'),
            'created_at'=> Carbon::now(),
            'updated_at'=> Carbon::now(),
        ]);

        // institute
        // The institute migration defines columns: name, establish, email, web, phoneNo, address
        DB::table('institute')->insert([
            'name' => 'Sample Institute',
            'establish' => '2020',
            'email' => 'info@sample.edu',
            'web' => 'https://sample.edu',
            'phoneNo' => '1234567890',
            'address' => '123 Main St',
            'created_at'=> Carbon::now(),
            'updated_at'=> Carbon::now(),
        ]);

        // department
        // Department code is unique; use firstOrCreate to avoid duplicate errors
        $department = DB::table('department')->where('code', 'CS')->first();
        if ($department) {
            $deptId = $department->id;
        } else {
            $deptId = DB::table('department')->insertGetId([
                'name' => 'Computer Science',
                'code' => 'CS',
                'credit'=> '3',
                'years'=> '4',
                'description'=> 'CS Department',
                'created_at'=> Carbon::now(),
                'updated_at'=> Carbon::now(),
            ]);
        }

        // subject (requires department_id)
        $subjectId = DB::table('subject')->insertGetId([
            'name' => 'Programming 101',
            'code' => 'CS101',
            'credit'=> '3',
            'description'=> 'Intro to programming',
            'levelTerm'=> '1',
            'department_id'=> $deptId,
            'created_at'=> Carbon::now(),
            'updated_at'=> Carbon::now(),
        ]);

        // student (requires department_id)
        // Student idNo is unique; use firstOrCreate via Eloquent model
        $student = Student::firstOrCreate(
            ['idNo' => 'S001'],
            [
                'session' => '2026',
                'department_id'=> $deptId,
                'bncReg'=> '2026',
                'batchNo'=> '2026',
                'firstName'=> 'Alice',
                'middleName'=> 'B',
                'lastName'=> 'Smith',
                'mobileNo'=> '5551234',
                'gender'=> 'F',
                'religion'=> 'None',
                'bloodgroup'=> 'O+',
                'nationality'=> 'Country',
                // dob column is DATE, use Y-m-d format
                'dob'=> Carbon::now()->subYears(20)->format('Y-m-d'),
                // photo is NOT NULL, provide a placeholder filename
                'photo'=> 'placeholder.jpg',
                'fatherName'=> 'Bob Smith',
                'fatherMobileNo'=> '5551111',
                'motherName'=> 'Carol Smith',
                'motherMobileNo'=> '5552222',
                // localGuardian fields are NOT NULL in migration; use empty strings
                'localGuardian'=> '',
                'localGuardianMobileNo'=> '',
                'presentAddress'=> '123 Home St',
                'parmanentAddress'=> '123 Home St',
                'isActive'=> 1,
                'created_at'=> Carbon::now(),
                'updated_at'=> Carbon::now(),
            ]
        );
        $studentId = $student->id;

        // registration (matches migration columns: levelTerm, session, students_id, department_id)
        DB::table('registrations')->insert([
            'levelTerm' => '1',
            'session'   => '2026',
            'students_id'=> $studentId,
            'department_id'=> $deptId,
            'created_at'=> Carbon::now(),
            'updated_at'=> Carbon::now(),
        ]);

        // attendance (matches migration columns)
        DB::table('attendances')->insert([
            'date' => Carbon::now()->format('Y-m-d'),
            'session' => '2026',
            'subject_id' => $subjectId,
            'students_id' => $studentId,
            'department_id' => $deptId,
            'levelTerm' => '1',
            'present' => 1,
            'created_at'=> Carbon::now(),
            'updated_at'=> Carbon::now(),
        ]);

        // sector (used by accounts)
        $sectorId = DB::table('sectors')->insertGetId([
            'name' => 'Income',
            'type' => 'Income',
            'created_at'=> Carbon::now(),
            'updated_at'=> Carbon::now(),
        ]);

        // account (sector foreign key)
        DB::table('accounts')->insert([
            'sectors_id'=> $sectorId,
            'amount'=> 1000,
            'date'=> Carbon::now()->format('Y-m-d'),
            'description'=> 'Sample income',
            'created_at'=> Carbon::now(),
            'updated_at'=> Carbon::now(),
        ]);

        // exam (students_id and subject_id)
        // exams table uses raw_score and requires department_id, session, levelTerm
        DB::table('exams')->insert([
            'department_id'=> $deptId,
            'session'=> '2026',
            'levelTerm'=> '1',
            'students_id'=> $studentId,
            'subject_id'=> $subjectId,
            'exam'=> 'Midterm',
            'raw_score'=> 85.00,
            'created_at'=> Carbon::now(),
            'updated_at'=> Carbon::now(),
        ]);

        // fee (department foreign key) – use title column as defined in migration
        $feeId = DB::table('fees')->insertGetId([
            'department_id'=> $deptId,
            'title'=> 'Tuition',
            'amount'=> 5000,
            'description'=> 'Tuition fee for 2026',
            'created_at'=> Carbon::now(),
            'updated_at'=> Carbon::now(),
        ]);

        // fee_collection – insert required columns (no direct fee_id reference)
        DB::table('fee_collections')->insert([
            'students_id'=> $studentId,
            'payableAmount'=> 5000.00,
            'lateFee'=> 0.00,
            'paidAmount'=> 5000.00,
            'dueAmount'=> 0.00,
            'payDate'=> Carbon::now()->format('Y-m-d'),
            'created_at'=> Carbon::now(),
            'updated_at'=> Carbon::now(),
        ]);

        // book (matches books migration columns) – use firstOrCreate to avoid duplicate code
        $book = DB::table('books')->where('code', 'BK001')->first();
        if ($book) {
            $bookId = $book->id;
        } else {
            $bookId = DB::table('books')->insertGetId([
                'code' => 'BK001',
                'title'=> 'Sample Book',
                'author'=> 'Author Name',
                'quantity'=> 5,
                'rackNo'=> 'A1',
                'rowNo'=> '1',
                'type'=> 'Textbook',
                'desc'=> 'Sample description',
                'department_id'=> $deptId,
                'created_at'=> Carbon::now(),
                'updated_at'=> Carbon::now(),
            ]);
        }

        // borrow_book (students_id and book_id)
        DB::table('borrow_books')->insert([
            'students_id'=> $studentId,
            'books_id'=> $bookId,
            // quantity is required in the migration
            'quantity'=> 1,
            'issueDate'=> Carbon::now()->format('Y-m-d'),
            'returnDate'=> Carbon::now()->addWeeks(2)->format('Y-m-d'),
            'fine'=> 0.00,
            'Status'=> 'Borrowed',
            'created_at'=> Carbon::now(),
            'updated_at'=> Carbon::now(),
        ]);

        // dormitory (matches dormitories migration columns)
        $dormId = DB::table('dormitories')->insertGetId([
            'name'=> 'Main Dorm',
            'numOfRoom'=> 100,
            'address'=> '123 Dorm St',
            'description'=> 'Main dormitory for students',
            'created_at'=> Carbon::now(),
            'updated_at'=> Carbon::now(),
        ]);

        // dormitory_student (matches migration columns)
        DB::table('dormitory_students')->insert([
            // migration uses 'dormitories_id' (plural) as foreign key
            'dormitories_id'=> $dormId,
            'students_id'=> $studentId,
            // required fields with placeholder values
            'joinDate'=> Carbon::now()->format('Y-m-d'),
            'leaveDate'=> null,
            'roomNo'=> '101',
            'monthlyFee'=> 200.00,
            'isActive'=> 'Yes',
            'created_at'=> Carbon::now(),
            'updated_at'=> Carbon::now(),
        ]);

        // dormitory_fee (link student to dormitory_student with fee amount)
        // First, get the ID of the dormitory_student record we just created
        $dormStudentId = DB::table('dormitory_students')
            ->where('dormitories_id', $dormId)
            ->where('students_id', $studentId)
            ->value('id');

        DB::table('dormitory_fees')->insert([
            'students_id'=> $studentId,
            'dormitory_students_id'=> $dormStudentId,
            'feeMonth'=> Carbon::now()->format('Y-m-01'),
            'feeAmount'=> 200.00,
            'created_at'=> Carbon::now(),
            'updated_at'=> Carbon::now(),
        ]);

        // re‑enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
