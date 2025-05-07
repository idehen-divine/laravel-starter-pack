# Laravel API Starter Pack

<p align="center">
  <a href="https://laravel.com" target="_blank">
    <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
  </a>
</p>

<p align="center">
  <a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
  <a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
  <a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
  <a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

---

## 🚀 About This Project

This **Laravel API Starter Pack** is built for clean, scalable API development using best practices. It integrates **Sanctum** for authentication, **Spatie** for roles and permissions, and follows a clean architecture using the **Service-Repository pattern** (based on Yaza Putu).

---

## 🔑 Key Features
- 🔒 Laravel Sanctum for API authentication
- 📚 Auto-generated API docs via Scribe
- 🛡 Role & Permission Management using Spatie ([Guide](https://spatie.be/docs/laravel-permission/v6/introduction)) 
- 🧱 Repository-Service Pattern ([Guide](https://yaza-putu.github.io/laravel-service-repository-pattern-guide/)) 
- 📐 Strong Request Validation with Form Requests 
- 📦 Enum-based permissions and roles
- ⚙️ Structured Helpers & Utility classes  
- ✉️ Mail support for Welcome & OTP-based flows  
- 🛠 Clean folder structure  
- 📘 Auto-generated API docs via Scribe  
- 🎯 Standard JSON response format  
- ✅ Queue-ready email system

---
## 🛠️ Getting Started

### ✅ Prerequisites

* PHP >= 8.1
* Composer
* MySQL or PostgreSQL
* Node.js & NPM (if you plan to build any frontend assets)

### ⚙️ Installation

```bash
# 1. Clone the repository
git clone https::/github.com/idehen-divine/laravel-starter-pack.git

# 2. Navigate into the project
cd laravel-starter-pack

# 3. Install backend dependencies
composer install

# 4. Copy environment variables
cp .env.example .env

# 5. Generate application key
php artisan key:generate

# 6. Run database migrations
php artisan migrate

# 7. Generate API documentation
php artisan scribe:generate
```


## 🗂 Project Structure

```

├── app/
│   ├── Enums/
│   │   ├── OTPMethodEnum.php
│   │   ├── OTPTypeEnum.php
│   │   ├── PermissionEnum.php
│   │   ├── RoleEnum.php
│   │   ├── ServerEnum.php
│   │   └── StatusEnum.php
│   ├── Helpers/
│   │   └── kernel.php
│   ├── Http/
│   │   ├── Controllers/
│   |   |   ├── Api/
│   |   |   |   ├── AuthController.php
│   |   |   |   ├── HomeController.php
│   |   |   |   └── UserController.php
│   |   |   └── Controller.php
│   │   ├── Requests/
│   |   |   ├── Auth/
│   |   |   |   ├── ForgotPasswordRequest.php
│   |   |   |   ├── LoginRequest.php
│   |   |   |   ├── RegisterRequest.php
│   |   |   |   ├── ResendOtpVerificationRequest.php
│   |   |   |   ├── ResetPasswordRequest.php
│   |   |   |   └── VerifyOtpRequest.php
│   |   |   └── User/
│   |   |       ├── UpdateUser2FAStatusRequest.php
│   |   |       ├── UpdateUserEmailRequest.php
│   |   |       ├── UserSearchRequest.php
│   |   |       ├── UserUpdateRequest.php
│   |   |       └── VerifyOtpRequest.php
│   │   └── Resources/
│   |       └── UserResource.php
│   ├── Mail/
│   │   └── OtpCodeMail.php
│   ├── Models/
│   |   ├── OtpCode.php
│   |   └── User.php
│   ├── Policies/
│   |   └── OwnerOrAdminPolicy.php
│   ├── Providers/
│   |   ├── AppService.php
│   |   └── OtpCodeProvider.php
│   ├── Repositories/
│   │   ├── OtpCode/
│   |   |   ├── OtpCodeRepository.php
│   |   |   └── OtpCodeRepositoryImplement.php
│   │   └── User/
│   |       ├── UserRepository.php
│   |       └── UserRepositoryImplement.php
│   └── Services/
│       ├── Auth/
│       |   ├── AuthService.php
│       |   └── AuthServiceImplement.php
│       ├── OtpCode/
│       |   ├── OtpCodeService.php
│       |   └── EmailServiceImplement.php
│       └── User/
│           ├── UserService.php
│           └── UserServiceImplement.php
├── database/
│   ├── factories/
│   │   ├── OtpCodeFactory.php
│   │   └── UserFactory.php
│   ├── migrations/
│   └── seeders/
│       ├── AdminSeeder.php
│       ├── DatabaseSeeder.php
│       └── RolePermissionSeeder.php
├── routes/
│   └── api.php
├── resources/
│   └── views/
│       └── emails/
│           ├── otp-mail.blade.php
│           ├── reset-email-otp-mail.blade.php
│           ├── reset-password-otp-mail.blade.php
│           ├── verify-2fa-otp-mail.blade.php
│           ├── verify-email-otp-mail.blade.php
│           └── welcome.blade.php
├── routes/
│   └── api.php
├── storage/
├── tests/
├── .env.example
├── artisan
├── composer.json
├── phpunit.xml
├── README.md
└── vite.config.js

```

---

## 🧰 Additional Features

### 📘 API Documentation

Generate API docs:

```bash
php artisan scribe:generate
```

Then visit:
[http://localhost:8000/docs](http://localhost:8000/docs)

---

### 🔐 Authentication

Authentication is handled using **Laravel Sanctum**.

#### Authentication Flow:

1. Register a new user
2. verify email otp
3. Log in to obtain an access token
4. Use the token in subsequent requests:

```
Authorization: Bearer {your-token}
```

---

### 👥 Roles & Permissions

Powered by **Spatie's Permission** package.
* [Spatie Docs](https://spatie.be/docs/laravel-permission/v6/introduction)

#### Default Roles:

* Owner
* Admin
* User

#### Sample Usage:

```php
$user->assignRole('admin');
$user->hasPermissionTo('edit articles');
```

### 📌 Enums

Take advantage of PHP 8.1 enums for defining constants in a type-safe way.

Generate and use enum classes via:

```bash
php artisan make:enum EnumName
```

Add the cases
```php
enum ServerEnum
{
    case LOCAL;
    case STAGING;
    case DEVELOPMENT;
    case PRODUCTION;
    case TESTING;
}
```
Use as such
```php
ServerEnum::LOCAL->name
```

### 📬 Mail System

#### OTP Emails

Dynamic OTP email content is generated using `OtpCodeMail`, with support for:

- Email Verification  
- Email Change  
- Password Reset  
- 2FA Authentication  
- 2FA Authorization  

**Enum-based view + subject selection** powered by `OTPTypeEnum`.

```php
Mail::to($user->email)->queue(new OtpCodeMail($otpCode));
```

#### Welcome Email

Make sure to add a `WelcomeMail` class and Blade view (`emails.welcome`) for onboarding new users.

---

### 🛠 Custom Helper Functions

Generate and use helper classes via:

```bash
php artisan make:helper HelperName
```

#### Usage:

```php
HelpersClassname::method('value');
helper()->className()->method('value');
helper()->className('value');
```

---

### 📦 Standard API Response Format

All responses return in a consistent format:

```json
{
    "code": "200",
    "message": "Operation successful",
    "data": {
        // Payload here
    }
}
```

### 🧱 Repository-Service Pattern

Architecture is based on:

* [Yaza Putu Guide](https://yaza-putu.github.io/laravel-service-repository-pattern-guide/)
* [GitHub Repository](https://github.com/yaza-putu/laravel-repository-with-service)

```php
class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index()
    {
        return $this->userService->getAllUsers();
    }
}
```

This keeps your business logic clean and scalable. **No usage example is provided here**,
 please refer to the official guide linked above.

---

### 🧪 Testing

```bash
php artisan test
```

---

## 🤝 Contributing

1. Fork this repository
2. Create a feature branch: `git checkout -b feature-name`
3. Commit your changes: `git commit -m "Add new feature"`
4. Push to the branch: `git push origin feature-name`
5. Create a pull request

---

## 🔐 Security

If you discover a security vulnerability, please contact [idehendivine16@gmail.com](idehendivine16@gmail.com) directly. Do **not** open an issue publicly.

---

## 📄 License

This project is open-source and licensed under the [MIT license](https://opensource.org/licenses/MIT).

