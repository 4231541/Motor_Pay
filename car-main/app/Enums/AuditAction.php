<?php

namespace App\Enums;

enum AuditAction: string
{
    case Registered = 'auth.registered';
    case Login = 'auth.login';
    case Logout = 'auth.logout';
    case PasswordChanged = 'auth.password_changed';
    case PasswordReset = 'auth.password_reset';
    case ProfileUpdated = 'auth.profile_updated';
    case EmailVerified = 'auth.email_verified';
}
