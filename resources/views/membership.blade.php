<div class="container-to-toggle" style="position:absolute;width: 70vw;height: 65px;left: 320px;top: 144px;;background-color:#D9D9D9; padding: 20px;border-radius: 8px;">
    <p style="position: absolute;color: #094042;font-size: 15px;">Unlock exclusive features for a deeper
        dive into the world of ebooks.</p>
    <a id="point_item" href="#" class="btn point_with_img" style="position: absolute;height:35spx;width: 260px; bottom: 14px; left: 83%; transform: translateX(-50%); background-color: #094042;color: white; border-radius: 7px;white-space: nowrap;">See
        the list of features </a>

    <!-- Second Container -->
    <div class="container-to-toggle" style="position:absolute;width:70vw;height:220px;left: 0px;top:85px;background-color:#D9D9D9; padding: 20px;border-radius: 8px;">
        <h1 style="font-family: 'Poppins', sans-serif; font-weight: 700; font-size: 55px; line-height: 50px; color: rgba(9, 64, 66, 1);">
            Want More? Go Pro!</h1>
        <p style="position: absolute;color: #094042;font-size: 23px;font-weight: 500;">You can track
            your entire library for free and <br> we'll NEVER take that from you. However, if you're
            craving <br> our ever-expanding extras, DixWix Pro has you covered!</p>
        <!-- Heading -->
    </div>
</div>

<!-- Third Container -->
<div class="container-to-toggle" style="position:absolute;width: 34vw;height: 390px;left: 319px;top: 472px;;background-color:#D9D9D9; padding: 20px;border-radius: 8px;">
    <img src="<?=url('assets/media/Ellipse 8.png')?>" alt="elip3" style="position: absolute;width: 50px;">
    <img src="<?=url('assets/media/book (1).png')?>" alt="elip4" style="position: absolute;width: 25px;left: 33px;top: 27px;">
    <h1>
        <span style="position:absolute;font-family:'Poppins', sans-serif; font-weight: 700; font-size: 30px; line-height: 45px;color: #094042;left:74px">Dix
            Wix Basic</span>
        <span style="position:absolute;font-family: 'Poppins', sans-serif; font-weight: 400; font-size: 24px; line-height: 36px;color: #094042;left: 285px;top: 25px;">(Free)</span>
    </h1>
    <p style="position: absolute;color: #094042;font-size: 17px;font-weight: 400;top: 75px;left: 75px;">
        Perfect for home libraries. Built for <br> personal collections.</p>
    <img src="<?=url('assets/media/group 4.png')?>" alt="gro" style="position: absolute;width: 25px;top: 140px;left: 75px;">
    <span style="position: absolute; top: 140px; left: 110px; font-family: 'Poppins', sans-serif; font-weight: 400; font-size: 16px; line-height: 30px; color: rgba(9, 64, 66, 1);">1
        Group</span>
    <img src="<?=url('assets/media/book (3) 1.png')?>" alt="gro" style="position: absolute;width: 25px;top: 170px;left: 73px;">
    <span style="position: absolute; top: 170px; left: 105px; font-family: 'Poppins', sans-serif; font-weight: 400; font-size: 16px; line-height: 30px; color: rgba(9, 64, 66, 1);">50
        Products ( Max ) </span>
    <img src="<?=url('assets/media/star 1.png')?>" alt="star" style="position: absolute;width: 25px;top: 210px;left: 75px;">
    <span style="position: absolute; top: 210px; left: 110px; font-family: 'Poppins', sans-serif; font-weight: 400; font-size: 16px; line-height: 30px; color: rgba(9, 64, 66, 1);">Users
        Rent products using points </span>

    <span style="position: absolute; top: 340px; left: 160px; font-weight: 400; font-size: 16px; line-height: 30px;">
        <?php if(isset($data['membership_plan_id']) && $data['membership_plan_id'] == 1){ ?>
        <span class="item-type-book">Activated Plan</span>
        <?php } elseif(auth()->check() && count(auth()->user()->membership) < 1){?>
        <a href="javascript:void(0)" class="dark-btn btn" style=""
            onclick="activate()">Activate
            Now</a>
        <?php }?>
    </span>
    <script>
        function activate(){
            const routeURL = '<?= route('activate-membership', ['plan_id' => 1]) ?>'
            alert(routeURL);
            window.location.href = routeURL;
        }
    </script>

    <!-- Heading -->

</div>
<!-- Fourth Container -->
<div class="container-to-toggle" style="position:absolute;width: 34vw;height: 390px;left: 813px;top: 472px;;background-color:#f7d8d8; padding: 20px;border-radius: 8px;">
    <img src="<?=url('assets/media/Ellipse 8.png')?>" alt="elip3" style="position: absolute;width: 50px;">
    <img src="<?=url('assets/media/book (2).png')?>" alt="elip4" style="position: absolute;width: 32px;left: 30px;top: 28px;">
    <h1>
        <span style="position:absolute;font-family:'Poppins', sans-serif; font-weight: 700; font-size: 30px; line-height: 45px;color: #094042;left:74px">Dix
            Wix Pro</span>
    </h1>
    <p style="position: absolute;color: #094042;font-size: 17px;font-weight: 400;top: 75px;left: 75px;">
        Upgraded for schools, organizations <br> & power users.</p>
    <span style="position:absolute;font-family:'Poppins', sans-serif; font-weight: 600; font-size: 25px;color: rgba(217, 78, 41, 1);top: 130px;left: 75px">$5/Month
        <br> $49/Year (18% Discount)</span>

    <img src="<?=url('assets/media/group 4.png')?>" alt="gro" style="position: absolute;width: 25px;top: 220px;left: 75px;">
    <span style="position: absolute; top: 220px; left: 110px; font-family: 'Poppins', sans-serif; font-weight: 400; font-size: 16px; line-height: 30px; color: rgba(9, 64, 66, 1);">5
        Group</span>

    <img src="<?=url('assets/media/book (3) 1.png')?>" alt="gro" style="position: absolute;width: 25px;top: 250px;left: 73px;">
    <span style="position: absolute; top: 250px; left: 105px; font-family: 'Poppins', sans-serif; font-weight: 400; font-size: 16px; line-height: 30px; color: rgba(9, 64, 66, 1);">500
        Products ( Max ) </span>

    <img src="<?=url('assets/media/star 1.png')?>" alt="star" style="position: absolute;width: 25px;top: 290px;left: 75px;">
    <span style="position: absolute; top: 290px; left: 110px; font-family: 'Poppins', sans-serif; font-weight: 400; font-size: 16px; line-height: 30px; color: rgba(9, 64, 66, 1);">*$1
        for each additional Group or 500 Products</span>
</div>
<!-- Fifth Container -->
<div class="container-to-toggle" style="position:absolute;width: 70vw;height: 100vh;left: 319px;top: 872px; border-radius: 8px;">
    <table>
        <thead>
            <tr style="border-radius: 15px;">
                <th>Website</th>
                <th>Basic</th>
                <th>Pro</th>
            </tr>
        </thead>
        <tbody>
            <tr style="background-color: #D9D9D9">
                <td>Catalog limit</td>
                <td class="checkmark-basic"><img src="<?=url('assets/media/close-circle.png')?>" alt="ch1" style="width: 25px;"></td>
                <td class="checkmark-pro"><img src="<?=url('assets/media/checkmark-circle.png')?>" alt="ch1" style="width: 25px;"></td>
            </tr>
            <tr>
                <td>Collection Limit</td>
                <td class="checkmark-basic"><img src="<?=url('assets/media/close-circle.png')?>" alt="ch1" style="width: 25px;"></td>
                <td class="checkmark-pro"><img src="<?=url('assets/media/checkmark-circle.png')?>" alt="ch1" style="width: 25px;"></td>
            </tr>
            <tr style="background-color: #D9D9D9">
                <td>Automatic meta data</td>
                <td class="checkmark-basic"><img src="<?=url('assets/media/close-circle.png')?>" alt="ch1" style="width: 25px;"></td>
                <td class="checkmark-pro"><img src="<?=url('assets/media/checkmark-circle.png')?>" alt="ch1" style="width: 25px;"></td>
            </tr>
            <tr>
                <td>Cloud sync</td>
                <td class="checkmark-basic"><img src="<?=url('assets/media/close-circle.png')?>" alt="ch1" style="width: 25px;"></td>
                <td class="checkmark-pro"><img src="<?=url('assets/media/checkmark-circle.png')?>" alt="ch1" style="width: 25px;"></td>
            </tr>
            <tr style="background-color: #D9D9D9">
                <td>Beautifu statistics</td>
                <td class="checkmark-basic"><img src="<?=url('assets/media/close-circle.png')?>" alt="ch1" style="width: 25px;"></td>
                <td class="checkmark-pro"><img src="<?=url('assets/media/checkmark-circle.png')?>" alt="ch1" style="width: 25px;"></td>
            </tr>
            <tr>
                <td>Mix media together</td>
                <td class="checkmark-basic"><img src="<?=url('assets/media/checkmark-circle.png')?>" alt="ch1" style="width: 25px;"></td>
                <td class="checkmark-pro"><img src="<?=url('assets/media/close-circle.png')?>" alt="ch1" style="width: 25px;"></td>
            </tr>
            <tr style="background-color: #D9D9D9">
                <td>Account switching</td>
                <td class="checkmark-basic"><img src="<?=url('assets/media/close-circle.png')?>" alt="ch1" style="width: 25px;"></td>
                <td class="checkmark-pro"><img src="<?=url('assets/media/close-circle.png')?>" alt="ch1" style="width: 25px;"></td>
            </tr>
            <tr>
                <td>Publish a list of you items</td>
                <td class="checkmark-basic"><img src="<?=url('assets/media/checkmark-circle.png')?>" alt="ch1" style="width: 25px;"></td>
                <td class="checkmark-pro"><img src="<?=url('assets/media/checkmark-circle.png')?>" alt="ch1" style="width: 25px;"></td>
            </tr>
            <tr style="background-color: #D9D9D9">
                <td>Create online reviews</td>
                <td class="checkmark-basic"><img src="<?=url('assets/media/close-circle.png')?>" alt="ch1" style="width: 25px;"></td>
                <td class="checkmark-pro"><img src="<?=url('assets/media/close-circle.png')?>" alt="ch1" style="width: 25px;"></td>
            </tr>
            <tr>
                <td>Import/Export your collections</td>
                <td class="checkmark-basic"><img src="<?=url('assets/media/checkmark-circle.png')?>" alt="ch1" style="width: 25px;"></td>
                <td class="checkmark-pro"><img src="<?=url('assets/media/checkmark-circle.png')?>" alt="ch1" style="width: 25px;"></td>
            </tr>
            <tr style="background-color: #D9D9D9">
                <td>Choose light or dark theme</td>
                <td class="checkmark-basic"><img src="<?=url('assets/media/close-circle.png')?>" alt="ch1" style="width: 25px;"></td>
                <td class="checkmark-pro"><img src="<?=url('assets/media/close-circle.png')?>" alt="ch1" style="width: 25px;"></td>
            </tr>
        </tbody>
    </table>
</div>
