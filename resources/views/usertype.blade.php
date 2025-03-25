<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', $data['title']) }}</title>

    <!-- Scripts -->
    @include('common.w_login.start_scripts')
</head>

<body>
    <div class="center-screen">
        <div style="border:2px solid;border-radius:5px;padding:15px;">
            <h2>Select Your Type</h2>
            <div class="container">
                <div class="row">
                    <?php foreach($data['group_types'] as $type){ ?>
                        <div class="col">
                            <a href="<?=route('save-user-type',["type_id"=>$type->id])?>" class="dark-btn btn link_with_img">
                                <?=$type->name?>
                            </a>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    
    @include('common.w_login.end_scripts')
</body>

</html>