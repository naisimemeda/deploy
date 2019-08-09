<?php
namespace Deployer;

require 'recipe/laravel.php';

set('repository', 'https://github.com/naisimemeda/hairpin.git');
add('shared_files', []);
add('shared_dirs', []);
set('writable_dirs', []);

host('120.27.242.187')
    ->user('root') // 使用 root 账号登录
    ->identityFile('~/.ssh/hairpin.pem') // 指定登录密钥文件路径
    ->become('www-data') // 以 www-data 身份执行命令
    ->set('deploy_path', '/var/www/hairpin-deployer'); // 指定部署目录
add('copy_dirs', ['vendor']);
after('deploy:failed', 'deploy:unlock');
before('deploy:symlink', 'artisan:migrate');
desc('Upload .env file');
task('env:upload', function() {
    // 将本地的 .env 文件上传到代码目录的 .env
    upload('.env', '{{release_path}}/.env');
});
before('deploy:vendors', 'deploy:copy_dirs');
// 定义一个后置钩子，在 deploy:shared 之后执行 env:upload 任务
after('deploy:shared', 'env:upload');
after('artisan:config:cache', 'artisan:route:cache');