@php
    $page_title = 'irTwitch - Add Streamer';
@endphp
@include('header')
<style>.modal-body p{line-height: 2.5;font-size: 16px;}</style>
<div class="row pt-5" style="direction: rtl;">
    <div class="modal modal-xl position-static d-block py-5" tabindex="-1" role="dialog" id="modalTour">
    <div class="modal-dialog" role="document">
        <div class="modal-content rounded-4 shadow">
        <div class="modal-body p-5">
            <h2 class="fw-bold mb-5 font display-5 text-center">اضافه کردن استریمر جدید</h2>
            <p>اینجا بهتون میگم چطوری استریمر جدید به سایت اضافه کنی.</p>
            <p>به طور کلی دو نوع اضافه کردن استریمر در ایران توییچ داریم، که هرکدوم رو در ادامه بهتون توضیح میدم.</p>
            <div class='bar'></div>
            <p style="font-size:24px; color:#da56f5;">اضافه کردن استریمر جدید، جهت نمایش در صفحه اصلی</p>
            <p>این نوع اضافه کردن خیلی سادس و توسط هر کسی میتونه انجام بشه و لازم نیست فقط استریمر درخواست ارسال کنه!</p>
            <p>فقط کافیه داخل <a href="https://discord.gg/vYGQQaqG5X" style="color:#da56f5;text-decoration: none;" target="_blank">سرور دیسکورد ما</a> عضو بشی و به چنل streamerhub بری.</p>
            <p class="text-center"><img src="{{ site_url }}uploads/2022-07-22_18.34.54.jpg"></p>
            <p>و روی دکمه Add New Streamer بزنی.</p>
            <p class="text-center"><img src="{{ site_url }}uploads/3842_2022-07-22_18.41.jpg" style="max-height: 200px;max-width: 100%;"></p>
            <p>حالا توی این صفحه، نام کاربری استریمری که میخوای توی سایت اضافه بشه رو در بخش TWITCH USERNAME وارد میکنی و روی دکمه Submit میزنی.</p>
            <p class="text-center"><img src="{{ site_url }}uploads/3844_2022-07-22_18.45.jpg" style="max-height: 200px;max-width: 100%;"></p>
            <p>مجدد یادت میندازم، این بخش رو حتی ویورها هم میتونن پر کنن و استریمر به سایت اضافه کنن، فقط دوتا نکته داره، اول اینکه استریمر باید در زمان ثبت، درحال استریم باشه و در نهایت توی عنوان استریمش هم باید Persian یا Farsi یا Iran داشته باشه، اگه بات بهت گفت همه چیز اوکیه، تا چند دقیقه بعدش استریمر به سایت اضافه میشه.</p>
            <div class='bar mt-5 mb-4'></div>
            <p style="font-size:24px; color:#da56f5;">اضافه کردن استریمر با قابلیت پخش در ایران توییچ</p>
            <p>این بخش مخصوص استریمراست، اگه استریمر هستی و میخوای استریمت علاوه بر توییچ توی ایران استریم هم پخش بشه، این بخش مخصوص خودته.</p>
            <p>اول از همه باید مطمئن بشی، که توی سایت ما ثبت شده باشی، اگه ثبت نیستی مراحل بالا رو یک بار انجام بده.</p>
            <p>حالا که استریمت داخل سایت ما ثبت شده، داخل همون چنل streamerhub روی دکمه Play Stream in irTwitch بزن.</p>
            <p class="text-center"><img src="{{ site_url }}uploads/3846_2022-07-22_18.59.jpg" style="max-height: 200px;max-width: 100%;"></p>
            <p>توی این پنجره در بخش اول، نام کاربری توییچت رو وارد کن و بخش دوم هم میانگین بیننده های استریمت رو وارد کن و روی دکمه Submit بزن.<p>
            <p class="text-center"><img src="{{ site_url }}uploads/3849_2022-07-22_19.01.jpg" style="max-height: 200px;max-width: 100%;"></p>
            <p>بعد از اینکه همه چیز رو درست وارد کردی، درخواستت بررسی میشه و وقتی تاییدش کنیم، از بات یک پیام دریافت میکنی و دیگه استریمهات داخل سایت ما هم پخش میشه.<p>
            <p>توجه داشته باشید این درخواست باید توسط خود استریمر ارسال بشه تا تایید کنیم وگرنه درخواست رد میشه.<p>
            <div class='bar mt-5 mb-4'></div>
            <p>استریم هایی که در صفحه اصلی سایت هستن و گوشه سمت راستشون لوگوی ایران توییچ دارن رو میتونین داخل سایت ما تماشا کنید.</p>
            <p class="text-center mt-2"><img src="{{ site_url }}template/logo-200x200.png" style="max-height: 100px;max-width: 100%;"></p>
        </div>
        </div>
    </div>
    </div>
</div>

@include('footer_js')
@include('footer')
