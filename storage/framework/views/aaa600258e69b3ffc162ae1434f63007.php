<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>Connexion - Gestion</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 400px;
            margin: 50px auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="email"], input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .btn {
            background-color: #3b82f6;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        .btn:hover {
            background-color: #2563eb;
        }
        .text-center {
            text-align: center;
        }
        .mb-4 {
            margin-bottom: 20px;
        }
        .mt-4 {
            margin-top: 20px;
        }
        .text-red-500 {
            color: #ef4444;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-center">Connexion</h2>
        
        <?php if(session('status')): ?>
            <div class="mb-4" style="background: #e3f2fd; padding: 10px; border-radius: 4px;">
                <?php echo e(session('status')); ?>

            </div>
        <?php endif; ?>

        <?php if(session('error')): ?>
            <div class="mb-4" style="background: #ffebee; color: #c62828; padding: 10px; border-radius: 4px;">
                <?php echo e(session('error')); ?>

            </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo e(route('login')); ?>">
            <?php echo csrf_field(); ?>

            <div class="form-group">
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="<?php echo e(old('email')); ?>" required autofocus>
                <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div class="text-red-500"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input id="password" type="password" name="password" required>
                <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div class="text-red-500"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" name="remember"> Se souvenir de moi
                </label>
            </div>

            <div class="text-center">
                <?php if(Route::has('password.request')): ?>
                    <a href="<?php echo e(route('password.request')); ?>">Mot de passe oublié?</a>
                <?php endif; ?>
                
                <button type="submit" class="btn">Se connecter</button>
            </div>
        </form>
        
        <div class="text-center mt-4">
            Pas encore de compte? <a href="<?php echo e(route('register')); ?>">S'inscrire</a>
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\gestion-magasin\resources\views/auth/login.blade.php ENDPATH**/ ?>