<?php
defined('C5_EXECUTE') or die("Access Denied.");

$this->inc('elements/header.php');
?>

<section class="banner clear">

<?php
$a = new Area('Banner');
$a->display();
 ?>


</section>

<section class="grid-outer clear left_wrapper">
<div class="container">
    <div class="row">
        <div class="col-12 col-lg-12">
          
            <h1>Page not found.</h1>
     
            <p>Sorry about this...</p>
          
            <p>You've followed a link for a page on our site that doesn't exist any more. We do remove content from time-to-time if it's no longer relevant, especially if it's only going to confuse readers.</p>
            <p>please don't hesitate to <a title="Contact" href="/contact-us">contact us</a> - we're happy to help.</p>
            <a href="/">Back to Home</a> </div>
        </div>
    </div>
</section>

<?php
$this->inc('elements/footer.php');
