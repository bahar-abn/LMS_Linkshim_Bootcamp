lms-php-mvc/
├── public/
│   ├── index.php                     ← مسئول: Member 1 (Bahar) - تنظیمات اولیه برنامه و روتینگ اصلی✅
│   └── assets/                       ← فایل‌های استاتیک مثل CSS, JS, تصاویر✅
├── core/
│   ├── Application.php              ← هسته برنامه - همه اعضا استفاده می‌کنند✅
│   ├── Router.php                   ← مسیریابی درخواست‌ها - Member 1 (Bahar) مدیریت کلی روتینگ✅
│   ├── Request.php                  ← مدیریت درخواست‌های HTTP✅
│   ├── Response.php                 ← مدیریت پاسخ‌ها✅✅
│   ├── Session.php                  ← مدیریت نشست‌ها - Member 1 (Bahar)✅
│   ├── Database.php                 ← اتصال دیتابیس و مدیریت مهاجرت‌ها - Member 1 (Bahar)✅
│   ├── MainController.php           ← کنترلر پایه (در صورت وجود مشترک)
│   └── migrations/
│       ├── m0001_create_users_table.php       ← Member 1 (Bahar) - ساخت جدول کاربران✅
│       ├── m0002_create_categories_table.php  ← Member 2 (Parnia) - ساخت جدول دسته‌بندی‌ها✅
│       ├── m0003_create_courses_table.php     ← Member 2 (Parnia) - ساخت جدول دوره‌ها✅
│       ├── m0004_create_enrollments_table.php ← Member 4 (Reza) - ساخت جدول ثبت‌نام‌ها ✅
│       └── m0005_create_reviews_table.php     ← Member 4 (Reza) - ساخت جدول نقد و بررسی✅
├── controllers/
│   ├── AuthController.php           ← Member 1 (Bahar) - ورود و ثبت‌نام✅
│   ├── CourseController.php         ← Member 2 (Parnia) - مدیریت دوره‌ها و دسته‌بندی‌ها
│   ├── AdminController.php          ← Member 3 (Mahyas) - پنل مدیریت و داشبوردها✅
│   └── ReviewController.php         ← Member 4 (Reza) - نقدها و ثبت‌نام‌ها✅
├── models/
│   ├── User.php                    ← Member 1 (Bahar) - مدل کاربر✅
│   ├── Course.php                  ← Member 2 (Parnia) - مدل دوره✅
│   ├── Category.php                ← Member 2 (Parnia) - مدل دسته‌بندی
│   ├── Enrollment.php              ← Member 4 (Reza) - مدل ثبت‌نام✅
│   └── Review.php                  ← Member 4 (Reza) - مدل نقد و بررسی✅
├── views/
│   ├── layout/
│   │   └── main.php                ← قالب اصلی سایت (شامل هدر، فوتر و ...)
│   ├── auth/
│   │   ├── login.php               ← Member 1 (Bahar)
│   │   └── register.php            ← Member 1 (Bahar)
│   ├── dashboard/
│   │   ├── admin.php               ← Member 3 (Mahyas)
│   │   ├── instructor.php          ← Member 3 (Mahyas)
│   │   └── student.php             ← Member 3 (Mahyas)
│   ├── courses/
│   │   ├── index.php               ← Member 2 (Parnia)
│   │   ├── create.php              ← Member 2 (Parnia)
│   │   └── details.php             ← Member 2 (Parnia)
│   └── reviews/
│       └── index.php               ← Member 4 (Reza)✅
├── config/
│   └── config.php                  ← Member 1 (Bahar) - تنظیمات دیتابیس و کلی برنامه✅
├── routes.php                     ← Member 1 (Bahar) - تعریف مسیرها (Route)
├── composer.json                  ← مدیریت بسته‌ها و Autoload
└── .htaccess                     ← تنظیمات وب سرور (مثل ری‌رایت‌ها)
