<?php defined('C5_EXECUTE') or die("Access Denied.");
$navItems = $controller->getNavItems();
$c = Page::getCurrentPage();
if (count($navItems) > 0) {
    $count = 0;
    $data = "";
    $data = '<ul class="navbar-nav">';
    foreach ($navItems as $ni) {
        if(!$ni->cObj->getAttribute('hide_from_side_navigation')){
        $classes = array();
        if ($ni->isCurrent) {
            $classes[] = 'nav-selected';
        }
        if ($ni->inPath) {
            $classes[] = 'nav-path-selected';
        }
        $ni->classes = implode(" ", $classes);

        if ($count == 0) {
            $parent_cid = Page::getByID($ni->cID)->getCollectionParentID();
            $page_name = Page::getByID($parent_cid)->getCollectionName();
            $pageLink = Page::getByID($parent_cid)->getCollectionLink();
            $head = "<h2><a href=" . $pageLink . " title=" . $page_name . ">" . $page_name . "</a></h2>";
            $count = 1;
        }
        if ($ni->hasSubmenu) {
            $has_sub = "has_sub";
        } else {
            $has_sub = "";
        }
        if ($c->getCollectionName() == $ni->name) {
            $open_class = ' open_nav';
        } else {
            $open_class = ' ';
        }
        $data .= '<li class="' . $ni->classes . $open_class . ' nav-item">'; //opens a nav item
        $data .= '<a href="' . $ni->url . '" target="' . $ni->target . '" class="' . $ni->classes . ' nav-link" title="' . h($ni->name) . '">' . h($ni->name) . '</a>';
        if ($ni->hasSubmenu) {
            $data .= '<span class="' . $has_sub . '"></span>';
        }
        if ($ni->hasSubmenu) {
            $data .= '<ul class="sub_nav">';
        } else {
            $data .= '</li>';
            $data .= str_repeat('</ul></li>', $ni->subDepth);
        }
    }
            }
    $data .= '</ul>';
    echo  '<div class="left_side" data-aos="fade-up"><div class="side_nav_c">' .$head . $data . '</div>'; ?>
    <?php }elseif(is_object($c) && $c->isEditMode()){ ?><div class="ccm-edit-mode-disabled-item"><?=t('Empty Auto-Nav Block.')?></div><?php }?>

    <script>
$(window).load(function(){
  if($('.sub_nav li').hasClass('nav-path-selected')){
$('.navbar-nav li.nav-path-selected').addClass('open_nav');}
});

$('li > span.has_sub').click(function(e) {
        $(this).parent().toggleClass("open_nav");
    });

//  $('li > span.has_sub').click(function(e) {
// $(this).parent().toggleClass("open_nav");
//    });
    </script>


   
</div>
