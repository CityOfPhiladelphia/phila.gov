<?php if(rwmb_meta('phila_v2_homepage_things_to_do_image','limit=1')): ?>

<div class="row mtl" style="margin-bottom:2rem;">

    <div class="large-24 columns" >

        <h2>Things To Do</h2>
        <img class="float-center" src=" <?= rwmb_meta('phila_v2_homepage_things_to_do_image','limit=1')[0]['full_url']  ?> " alt="">
    </div>

</div>

<?php endif; ?>
