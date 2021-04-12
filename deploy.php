<?php
namespace Deployer;

require 'recipe/laravel.php';

// Project name
set('application', 'DrCallAway');

// Rsynme - drcall
// require 'recipe/rsync.php';


// set('rsync_src', function () {
//     return __DIR__;
// });

// Project repository
set('repository', 'git@github.com:ishgem007/drcallaway-api.git'); 

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', false); 
set('branch', 'aws-deploy');

//drcall setting
// add('rsync', [
//     'exclude' => [
//         '.git',
//         '/.env',
//         '/storage/',
//         '/vendor/',
//         '/node_modules/',
//         '.github',
//         'deploy.php',
//     ],
// ]);

task('deploy:secrets', function () {
    file_put_contents(__DIR__ . '/.env', getenv('DOT_ENV_PRODUCTION'));
    upload('.env', get('deploy_path') . '/shared');
});

// Shared files/dirs between deploys 
add('shared_files', []);
add('shared_dirs', []);

// Writable dirs by web server 
add('writable_dirs', []);


// Hosts

host('3.17.163.183')
    ->user('devs')
    ->identityFile('~/.ssh/deployerkey')
    ->set('deploy_path', '/var/www/html/drcall-app/');    
    
// Tasks

// task('build', function () {
//     run('cd {{release_path}} && build');
// });

task('artisan:optimize', function () {});

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');


//drcall setting
desc('Deploy the application');


// task('deploy', [
//     'deploy:info',
//     'deploy:prepare',
//     'deploy:lock',
//     'deploy:release',
//     'rsync',
//     'deploy:secrets',
//     'deploy:shared',
//     'deploy:vendors',
//     'deploy:writable',
//     'artisan:storage:link',
//     'artisan:view:cache',
//     'artisan:config:cache',
//     'artisan:migrate',
//     'artisan:queue:restart',
//     'deploy:symlink',
//     'deploy:unlock',
//     'cleanup',
// ]);

// Migrate database before symlink new release.

// before('deploy:symlink', 'artisan:migrate');

