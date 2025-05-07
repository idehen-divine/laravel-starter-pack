<?php

namespace App\Enums;

enum PermissionEnum
{
    case ACCESS_USER;
    case ACCESS_ADMIN_PANEL;
    case VIEW_USERS;
    case EDIT_USERS;
    case DELETE_USERS;
    case VIEW_ROLES;
    case EDIT_ROLES;
    case VIEW_PERMISSION;
    case EDIT_PERMISSION;
    case VIEW_SETTINGS;
    case EDIT_SETTINGS;
    case DELETE_SETTINGS;
    case VIEW_LOGS;
    case DELETE_LOGS;
}
