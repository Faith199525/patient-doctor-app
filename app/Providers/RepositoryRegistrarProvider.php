<?php

namespace App\Providers;


use App\Repository\DoctorRepositoryImpl;
use App\Repository\PartnersRepositoryImpl;
use App\Repository\RequestedPartnersRepositoryImpl;
use App\Repository\PermissionRepositoryImpl;
use App\Repository\PrescriptionRepositoryImpl;
use App\Repository\RoleRepositoryImpl;
use App\Repository\UserRepositoryImpl;
use App\RepositoryContracts\DoctorRepository;
use App\RepositoryContracts\PartnersRepository;
use App\RepositoryContracts\RequestedPartnersRepository;
use App\RepositoryContracts\PermissionRepository;
use App\RepositoryContracts\PrescriptionRepository;
use App\RepositoryContracts\RoleRepository;
use App\RepositoryContracts\UserRepository;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class RepositoryRegistrarProvider extends ServiceProvider implements DeferrableProvider
{

    /**
     * All of the container singletons that should be registered.
     *
     * @var array
     */
    public $singletons = [
        RoleRepository::class => RoleRepositoryImpl::class,
        UserRepository::class => UserRepositoryImpl::class,
        PermissionRepository::class => PermissionRepositoryImpl::class,
        DoctorRepository::class => DoctorRepositoryImpl::class,
        PrescriptionRepository::class => PrescriptionRepositoryImpl::class,
        PartnersRepository::class => PartnersRepositoryImpl::class,
        //RequestedPartnersRepository::class => RequestedPartnersRepositoryImpl::class,
    ];

    public function provides()
    {
        return array_keys($this->singletons);
    }
}
