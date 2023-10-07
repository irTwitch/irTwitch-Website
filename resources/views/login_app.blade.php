@include('header')

<div class="row pt-5" style="direction: rtl;">
    <div class="modal modal-tour position-static d-block py-5" tabindex="-1" role="dialog" id="modalTour">
        <div class="modal-dialog" role="document">
            <div class="modal-content rounded-4 shadow">
                <div class="modal-body p-5">
                    <h2 class="fw-bold mb-0 font display-5 text-center">ورود به سایت</h2>
                    <ul class="d-grid gap-4 my-5 list-unstyled">
                        <li class="d-flex gap-4">
                            <span class="bx bx-question-mark" style="font-size: 60px;"></span>
                            <div>
                            <h5 class="mb-3 h2">چرا باید وارد اپلیکیشن بشم؟</h5>
                            <p style="line-height: 2;">برای دسترسی به برخی بخشها مانند چت، شما باید وارد اپلیکیشن بشید.</p>
                            </div>
                        </li>
                        <li class="d-flex gap-4 mt-5">
                            <span class="bx bxl-twitch" style="font-size: 60px;"></span>
                            <div>
                            <h5 class="mb-3 h2">چطوری لاگین کنم؟</h5>
                            <p style="line-height: 2;">ابتدا فیلترشکن خود را روشن کنید، سپس روی دکمه زیر بزنید و وارد اکانت توییچتون بشید و دسترسی لازم رو به اپلیکیشن بدید. در انتها فیلترشکن رو خاموش کنید.</p>
                            </div>
                        </li>
                    </ul>
                    <a href="https://id.twitch.tv/oauth2/authorize?response_type=code&client_id={{ MAIN_TWITCH_BOT_TOKEN['client_id'] }}&redirect_uri={{ site_url }}login_app.php&force_verify=true&scope=user:read:follows%20chat:edit%20channel:moderate%20chat:read&state=,1" class="btn btn-lg btn-primary mt-0 w-100">ورود/ثبت نام و رفتن به توییچ</a>
                </div>
            </div>
        </div>
    </div>
</div>


@include('footer_js')
@include('footer')
