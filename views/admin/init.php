<div class="row">
    <?php for($i=$min_year;$i<=date('Y');$i++){?>
        <div class="col-lg-4">
            <div class="card bg-primary">
                <div class="card-content">
                                <span class="card-title"><?=$i?></span>
                                <p></p>
                                </div>
                                <div class="card-action">
                                    <a href="<?=base_url('admin/depositos/'.$i)?>" class="btn btn-default color-primary"><span>Administrar</span></a>
                                    
                                </div>
            </div> 
        </div>


    <?php }?>
</div>