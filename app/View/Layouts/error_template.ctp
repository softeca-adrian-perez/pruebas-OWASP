<style>
    * {
        font-family: arial;
    }

    .cnt-no-encontrado {
        width: 100%;
        height: 100%;
        position: fixed;
        top: 0;
        left: 0;
        text-align: center;
        overflow: auto;
        background-image: url('/css/img/left-bottom.png'), url('/css/img/top-right.png'), url('/css/img/connected-car.png'), url('/css/img/connected-car.png');
        background-position: left bottom, top right, top 5% left 10%, bottom 10% right 5%;
        background-repeat: no-repeat, no-repeat, no-repeat, no-repeat;
        background-size: 25% auto, 25% auto, 10% auto, 10% auto;
    }

    .cnt-no-encontrado>div {
        width: 100%;
        max-width: 400px;
        margin: 0 auto;
        display: inline-block;
        font-size: 35px;
        color: #0064ae;
        font-weight: 300;
    }

    .cnt-no-encontrado>div>div {
        padding: 15rem 1rem 1rem 1rem;
    }

    .cnt-no-encontrado>div>div>div {
        text-align: left;
        font-size: 17px;
        line-height: 1.5;
        text-align: center;
    }

    .cnt-no-encontrado>div>div>div>div {
        text-align: center;
        padding: 20px 0;
    }

    .cnt-no-encontrado strong {
        display: block;
        font-size: 140px;
        border-bottom: 1px solid #0064ae;
        color: #0064ae;
        font-weight: bold;
    }

    span {
        font-size: 17px;
        color: #000;
        display: block;
    }

    .cnt-no-encontrado a {
        font-size: 14px;
        color: #fff;
        background-color: #0064ae;
        text-decoration: none;
        border-radius: 5px;
        padding: 8px 12px;
        font-weight: bold;
        text-align: center;
        display: inline-block;
        margin-top: 20px;
    }

    @media (max-width: 640px) {
        .cnt-no-encontrado>div>div {
            padding: 3rem 1rem 1rem 1rem;
        }
    }
</style>
<div class="cnt-no-encontrado">
    <div>
        <div>
            <?php echo strtoupper(__t('Error.Error')); ?>
            <strong>
                404
            </strong>
            <div>
                <div>
                    <?php echo strtoupper(__t('General.Sorry_this_page_is_not_found')); ?>
                </div>
                <span>
                    <?php echo strtoupper(__t('General.Oops_you_may_have_mistyped_the_address_or_the_page_may_be_moved')); ?>
                </span>
                <a href="/"><?php echo strtoupper(__t('General.Take_me_back_to_the_home_page')); ?></a>
            </div>
        </div>
        <br />
        <img src="/img/aag.svg" alt="aag" style="width: 175px">
    </div>
</div>