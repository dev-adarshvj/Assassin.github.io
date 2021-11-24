<?php namespace RamonLeenders\BlockDesignerPro\FieldType\BlockIdFieldType;

defined('C5_EXECUTE') or die("Access Denied.");

use RamonLeenders\BlockDesigner\FieldType\FieldType;

class BlockIdFieldType extends FieldType
{
    protected $ftHandle = 'block_id';
    protected $requiredSlug = false;
    protected $useBaseFields = false;
    protected $canRepeat = true;

    public function getFieldName()
    {
        return t("Block ID");
    }

    public function getFieldDescription()
    {
        return t("Output the Block's ID (variable %s within the view file). This is a unique number for each and every block", '$bID');
    }

    public function getViewContents()
    {
        return '<?php echo $bID; ?>';
    }
}