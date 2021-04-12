<?php
/**
 *  * Author: Akanbi Lawal
 * Date: 27/05/2020
 * Time: 9:29 PM
 */

namespace App\Providers;

use App\Service\PartnersServiceImpl;
use App\Service\RequestedPartnersServiceImpl;
use App\Service\PermissionServiceImpl;
use App\Service\PrescriptionServiceImpl;
use App\Service\DoctorManagementServiceImpl;
use App\Service\UserManagementServiceImpl;
use App\ServiceContracts\PartnersService;
use App\ServiceContracts\RequestedPartnersService;
use App\ServiceContracts\PermissionService;
use App\ServiceContracts\PrescriptionService;
use App\ServiceContracts\RoleService;
use App\Service\RoleServiceImpl;
use App\ServiceContracts\DoctorManagementService;
use App\ServiceContracts\UserManagementService;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;


class ServiceRegistrarProvider extends ServiceProvider implements DeferrableProvider
{


    /**
     * All of the container singletons that should be registered.
     *
     * @var array
     */
    public $singletons = [
        UserManagementService::class => UserManagementServiceImpl::class,
        DoctorManagementService::class => DoctorManagementServiceImpl::class,
        RoleService::class => RoleServiceImpl::class,
        PermissionService::class => PermissionServiceImpl::class,
        PrescriptionService::class => PrescriptionServiceImpl::class,
        PartnersService::class => PartnersServiceImpl::class,
        RequestedPartnersService::class => RequestedPartnersServiceImpl::class,
    ];


    public function provides()
    {
        return array_keys($this->singletons);
    }
}
