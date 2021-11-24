<?php defined('C5_EXECUTE') or die("Access Denied.");
$navItems = $controller->getNavItems();
$c = Page::getCurrentPage();
if (count($navItems) > 0) {
    $count = 0;
    $data = "";
    $data = '<ul class="navbar-nav">';
    foreach ($navItems as $ni) {
     if($ni->cObj->getAttributeValue('mobile_nav')) {
        $classes = array();
        if($ni->isCurrent){$classes[] = 'nav-selected';}
        if($ni->inPath){$classes[] = 'nav-path-selected';}
        $ni->classes = implode(" ", $classes);

        //$head = '<ul><li class="nav-item"><a href="/" target="_self" class="nav-link" title="Home">Home</a></li></ul>';

        if($ni->hasSubmenu){
            $has_sub = "has_sub_footer";
            }
            else{
            $has_sub = "";
            }
            if($c->getCollectionName() == $ni->name){
              $open_class = ' open_nav_footer';
            }else{
                $open_class = ' ';
            }
            $data .= '<li class="' . $ni->classes. $open_class.' nav-item">'; //opens a nav item
            $data .= '<a href="' . $ni->url . '" target="' . $ni->target . '" class="' . $ni->classes . ' nav-link" title="' . h($ni->name) . '">' . h($ni->name) . '</a>';
            if($ni->hasSubmenu){
            $data .= '<span class="'.$has_sub.'"></span>';
             }
        if($ni->hasSubmenu){
            $data .= '<ul class="footer-sub-nav">';
            }else{
            $data .= '</li>';
            $data .= str_repeat('</ul></li>', $ni->subDepth);
            }

          }  }
    $data .= '</ul>';
    echo  '<div class="footer-nav-link clear">' . $data . '</div>'; ?>
    <?php }elseif(is_object($c) && $c->isEditMode()){ ?><div class="ccm-edit-mode-disabled-item"><?=t('Empty Auto-Nav Block.')?></div><?php }?>
