<?php

namespace App\Enums;

enum PermissionName: string
{
    case UsersView = 'users.view';
    case UsersCreate = 'users.create';
    case UsersUpdate = 'users.update';
    case UsersDelete = 'users.delete';
    case UsersAssignRoles = 'users.assign_roles';

    case RolesView = 'roles.view';
    case RolesManage = 'roles.manage';

    case BrandsManage = 'brands.manage';
    case CarModelsManage = 'car_models.manage';

    case CarsViewAll = 'cars.view_all';
    case CarsCreate = 'cars.create';
    case CarsUpdate = 'cars.update';
    case CarsDelete = 'cars.delete';
    case CarsApprove = 'cars.approve';

    case RequestsCreate = 'requests.create';
    case RequestsViewAll = 'requests.view_all';
    case RequestsUpdateStatus = 'requests.update_status';

    case AuditLogsView = 'audit_logs.view';
}
