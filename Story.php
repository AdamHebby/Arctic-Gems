<?php 
class Story {
    protected $scenes = array();
    protected $ids = array();

    function __construct()
    {
        $this->scenes = array();
        $this->ids = array();
        $this->storyJson = file_get_contents('Files/Story.json');
    }
    public function loadScenes()
    {
        $json = $this->storyJson;
        $json = json_decode($json, true);
        $json = $json["scenes"];
        foreach ($json as $k => $v) { // Each scene
            $id = $k;
            $name = $v["name"];
            $text = $v["text"];
            if (isset($v["give"])) {
                $give = $v["give"];
            } else {
                $give = null;
            }
            if (isset($v["xp"])) {
                $giveXP = $v["xp"];
            } else {
                $giveXP = "0";
            }
            $options = $v["options"];
            $optionObjArr = array();
            $opNum = 1;
            foreach ($options as $key => $value) {
                $opGoto = $options["op"]["goto"];
                $opText = $options["op"]["text"];
                if (isset($options["op"]["requireditems"])) {
                    $opRequireditems = $options["op"]["requireditems"];
                } else {
                    $opRequireditems = null;
                }
                if (isset($options["op"]["give"])) {
                    $opGive = $options["op"]["give"];
                } else {
                    $opGive = null;
                }
                $newOption = new Option($opGoto, $opText, $opGive, $opNum, $opRequireditems);
                array_push($optionObjArr, array("$opNum" => $newOption));
                $opNum++;
            }

            $newScene = new Scene($id, $name, $text, $give, $giveXP, $optionObjArr, "scene");
            $this->addScene($newScene);
        }
    }
    public function isEmpty()
    {
        return (empty($this->scenes));
    }
    public function addScene(Scene $scene)
    {
        $id = $scene->getId();
        if (!$id) throw new Exception('The Story requires scenes with unique ID values.');
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
class Option {
    protected $text;
    protected $opNumber;
    protected $give;
    protected $next;
    protected $reqItems = array();

    function __construct($next, $text, $give, $opNumber, $reqItems)
    {
        $this->text = $text;
        $this->opNumber = $opNumber;
        $this->reqItems = $reqItems;
        $this->give = $give;
        $this->next = $next;
    }
}