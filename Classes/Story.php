<?php
namespace Story;

class Story
{
    protected $scenes = array();
    protected $ids = array();

    public function __construct() 
    {
        $this->scenes = array();
        $this->ids = array();
        $this->storyJson = file_get_contents('Files/Story.json');
    }
    public function loadScenes($Inventory)
    {
        $json = $this->storyJson;
        $json = json_decode($json, true);
        $json = $json["scenes"];
        foreach ($json as $k => $v) { // Each scene
            $id = $k;
            $name = $v["name"];
            $text = $v["text"];
            $give = isset($v["give"]) ? $v["give"] : null;
            $giveXP = isset($v["xp"]) ? $v["xp"] : 0;
            $options = $v["options"];
            $optionObjArr = array();
            for ($opNum=1; $opNum < count($options) + 1; $opNum++) { 
                $opGoto = isset($options["op-".$opNum]["goto"]) ? $options["op-".$opNum]["goto"] : null;
                $opText = $options["op-".$opNum]["text"];
                $opRequireditems = isset($options["op-".$opNum]["requireditems"]) ? $options["op-".$opNum]["requireditems"] : null;
                $opRequireditemObjects = array();
                if ($opRequireditems != null) {
                    foreach ($opRequireditems as $item) {
                        $itemId = $item["id"];
                        $qty = $item["count"];
                        $reqItem = $Inventory->getItemByID($item["id"]);
                        $index = array_search($reqItem->getId(), $opRequireditems);
                        $opRequireditemObjects[$itemId] = array("item" => $reqItem, "qty" => $qty);
                    }
                }
                $opGive = isset($options["op-".$opNum]["give"]) ? $options["op-".$opNum]["give"] : null;
                $newOption = new Option($opGoto, $opText, $opGive, $opNum, $opRequireditemObjects);
                $optionObjArr["op-".$opNum] = $newOption;
            }

            $newScene = new Scene($id, $name, $text, $give, $giveXP, $optionObjArr, "scene");
            $this->addScene($newScene);
        }
    }
    public function getScene($name)
    {
        return $this->scenes[$name];
    }
    public function isEmpty()
    {
        return (empty($this->scenes));
    }
    public function addScene(Scene $scene)
    {
        $id = $scene->getId();
        if (!$id) {
            throw new Exception('The Story requires scenes with unique ID values.');
        }
        $this->scenes[$id] = array('scene' => $scene);
        $this->ids[] = $id;
    }
    public function updateScene(Scene $scene, $qty)
    {
        $id = $scene->getId();
    }
    public function deleteScene(Scene $scene)
    {
        $id = $scene->getId();
        if (isset($this->scenes[$id])) {
            unset($this->scenes[$id]);
            $index = array_search($id, $this->ids);
            unset($this->ids[$index]);
            $this->ids = array_values($this->ids);
        }
    }
}
