<style>
    .row{
        display:flex;
        justify-content: center;
        align-items: center;
    }
    a{
        text-decoration: none;
    }
    .col{
        display:flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }
    .col .btn{
        padding:5px;
        display:flex;
        justify-content: center;
        background:sienna;
        color:#fff;
        border-radius:4px;
        text-decoration:none;
    }
    .col .footer{
        margin-top:50px;
    }
</style>

<div class="row">
    <div class="col">  
        <h3>kindly click the button below to verify your email</h3> <br> 
        <a href="{{$link}}" class="btn" style="
        text-decoration:none;
        color: white;
        background: #814baa;
        border: 1px #DADADA solid;
        padding: 5px 10px;
        border-radius: 2px;
        font-weight: bold;
        font-size: 9pt;
        outline: none;">
            Verify</a><br><br>
        <small style="align-self: flex-start;margin-top:50px;">Thank You,</small><br><br><br>
        <small class="footer"><a href="cashdrive.co">Drcallaway</a> &copy; 2020 | 09089787978</small>
    </div>
</div>
