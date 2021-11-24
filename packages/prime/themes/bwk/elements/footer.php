<?php $p = Page::getCurrentPage();
defined('C5_EXECUTE') or die("Access Denied.");?>
<footer class="clear"  data-aos="fade-up">
            <div class="container">
                <div class="row">
                    <div class="col-12 col-md-12 col-lg-4">
                        <div class="footer_logo">
                           <?php $a = new GlobalArea('Footer Logo'); $a->display(); ?>
                        </div>
                        <div class="footer_address">
                            <?php $a = new GlobalArea('Footer Address'); $a->display(); ?>
                        </div>

                    </div>
                    <div class="col-12 col-md-9 col-lg-6">
                        <div class="footer_nav">
                            <?php $a = new GlobalArea('Footer Nav'); $a->display(); ?>
                            <div class="footer_nav_wrap d-lg-block d-none">
                            <?php $a = new GlobalArea('Footer Sub Nav'); $a->display(); ?>
                             </div>

                        </div>

                    </div>
                    <div class="col-12 col-md-3 col-lg-2">
                        <div class="footer_btn">
                           <?php $a = new GlobalArea('Footer Button'); $a->display(); ?>
                        </div>


                    </div>

                </div>

                <div class="row">
                  <div class="col-md-12 footer_nav">
                  <div class="footer_nav_wrap d-lg-none d-md-block d-none">
                  <?php $a = new GlobalArea('Footer Sub Nav'); $a->display(); ?>
                   </div>
                   </div>
                </div>

            </div>
        </footer>

<?php $this->inc('elements/footer_bottom.php');?>
