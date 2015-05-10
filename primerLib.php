<?php
/*
    Copyright 2014 Po-Hao Chen.
    This file is part of Centaur.

    Centaur is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

session_start();
/*
    Connect to your database here.
*/

$db = new mysqli('web.url.com', '', '','');


if (mysqli_connect_errno($db)) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

class Quiz  {
	private $questions = array();
    private $id;
    private $URLbase;
    private $title;
    private $author;
    private $cover;
    private $divert;
	private $timer;
	private $confirm;

    function __construct($id, $base) {
        $this->id = $id;
        $this->URLbase = $base;
    }

    function getCover()  { 
        if (isset($this->cover)) return $this->cover; 
        else return False;
    }
    function setCover($c)  {
        $this->cover = $c;
    }
    
    function getDivertModule()  {
        // This is the module user is sent to if they do not consent.
        return $this->divert;
    }
    function setDivertModule($d)  {
        $this->divert = $d;
    }
    function setTitle($title) { $this->title = $title; }
    function getTitle() { return $this->title; }
    function getID() { return $this->id; }
    function getURL() { return $this->URLbase;}
	function addQuestion($q)  { 
        $q->setID(sizeof($this->questions));
        $this->questions []= $q; 
    }
    function getQuestions() { return $this->questions; }
    function setAuthor($au) { $this->author = $au; }
    function getAuthor() { return $this->author; }
    function getAnswerText($qindex, $aindex) { 
        $ques = $this->questions[$qindex];
        $answers = $ques->getAnswerChoices();
        return $answers[$aindex];
    }
	function setConfirmStatus($c)  { $this->confirm = $c; }
	function getConfirmStatus()  { return $this->confirm; }
	function setDefaultTimer($t)  { $this->timer = intval($t); }
	function getDefaultTimer() { return $this->timer; }
    function randomize($tag)  {
        $post = array();
        $rand = array();

        $start = False;

        foreach ($this->questions as $q)  {
            if ($q->getRandomizeTag() == $tag) {
                $rand []= $q;
                $start = True;
            } else  {
                if ($start) {
                    shuffle($rand);
                    $post = array_merge($post, $rand);
                    $rand = array();
                }
                $post []= $q;
            }
        }
        if (sizeof($rand) > 0) {
            shuffle($rand);
            $post = array_merge($post, $rand);
        }
        $this->questions = $post;
    }

    function randomizeAll($self) {
        $tags = array();
        foreach ($this->questions as $q)  {
            if ($q->getRandomizeTag())  {
                $t = $q->getRandomizeTag();
                $tags []= $t;
            }
        }
        $tags = array_unique($tags);
        foreach ($tags as $t)  {
            $self->randomize($t);
        }
    }
}

class Question  {
    private $answers = array();
    private $text = "";
    private $img = array();
    private $ansimg = array();
    private $feedback = False;
    private $randomizeTag = "";
    private $qid = -1;
    private $required = False;
	private $timer = 0;

    function __construct($t, $ans=array()) {
        $this->text = $t;
        $this->answers = $ans;
    }
	function setTimer($t)  { $this->timer = $t; }
	function getTimer() { return $this->timer; }
    function setRequired($r)  {$this->required = $r;}
    function isRequired() {return $this->required;}
    function getID()  { return $this->qid; }
    function setID($id) { $this->qid = $id; }
    function isRandomized()  { return $this->randomizeTag==""?False:True; }
    function getRandomizeTag()  { return $this->randomizeTag; }
    function setRandomizeTag($r) { $this->randomizeTag = $r;}
    function getAnswerChoices() { return $this->answers; }
    function getAnswerPoints() {
        $answerIdx = array();
        foreach ($this->answers as $a) {
            $answerIdx []= $a->getValue();
        }
        return $answerIdx;
    }

    function getAnswerIndices()  {
        $ind = 0;
        $answerIdx = array();
        foreach ($this->answers as $a) {
            if ($a->hasValue()) $answerIdx []= $ind;
            $ind++;
        }
        return $answerIdx;
    }
    function setFeedback($feed)  {
        $this->feedback = $feed;
    }
    function hasFeedback() { return $this->feedback; }
    function getQuestionText() { return $this->text; }
    function isMultipleChoice()  { return sizeof($this->answers)>1?True:False; }
    function isFreeText() { return sizeof($this->answers)==0?True:False; }
    function addAnswer($ans) {
        $ans->setID(sizeof($this->answers));
        $this->answers []= $ans;
    }
    function addImage($filename) { $this->img []= $filename; } 
    function addAnswerImage($filename) { $this->ansimg []= $filename; } 
    function hasImage() { return sizeof($img)>0?True:False; }
    function hasAnswerImage() { return sizeof($ansimg)>0?True:False; }
    function getImages() { return $this->img; }
    function getAnswerImages() { return $this->ansimg; }
    function getTags()  {
        $tags = array();
        foreach ($this->answers as $ans)  {
            $t = $ans->getTag();
            if (!in_array($t, $tags)) $tags []= $t;
        }
        return $tags;
    }

    function getAnswersByTag($tag)  {
        $ansList = array();
        foreach ($this->answers as $ans)  {
            if ($ans->getTag() == $tag) $ansList []= $ans;
        }
        return $ansList;
    }
    function getMaxPossiblePoint($tag)  {
        $max = 0;
        foreach ($this->answers as $ans)  {
            if ($ans->getValue() > $max && $tag = $ans->getTag())  {
                $max = $ans->getValue();
            }
        }
        return $max;
    }
    function getPointValueForAnswer($input, $tag)  {
        foreach ($this->answers as $ans)  {
            if ($ans->getAnswerText() == $input && $tag == $ans->getTag())  {
                return $ans->getValue();
            }
        }
        return 0;
    }
}

class Answer  {
    private $text = "";
    private $pointvalue = 0;
    private $id = -1;
    private $tag = "default";
    function __construct($t, $val=0) {
        $this->text = $t;
        $this->pointvalue = $val;
    }
    function setTag($t)  { $this->tag = $t; }
    function getTag()  { return $this->tag; }
    function setID($i) { $this->id = $i; }
    function getID() { return $this->id; }
    function hasValue() { return $this->pointvalue>0?True:False; }
    function setValue($v) { $this->pointvalue = $v; }
    function getValue() { return $this->pointvalue; }
    function getAnswerText() { return $this->text; }
}

function addAnswer($a, $question)  {
    $ans = getValue($a, "TEXT");
    $tag = getValue($a, "TAG");
    $pt = 0;
    if (isset($a['CORRECT'])) $pt = 1; 
    else if (isset($a['HALF'])) $pt = 0.75; 
    else if (isset($a['POINT']))  {
        $pt = floatval($a['POINT']);
    }
    $newAnswer = new Answer($ans, $pt);
    if ($tag) $newAnswer->setTag($tag);
    $question->addAnswer($newAnswer);
}
function getValue($input, $v)  {
	if (isset($input[$v])) return $input[$v];
	else return False;
}
function getDirFromID($gameID) {
    global $db;
    $sql = "SELECT directory_name FROM quizzes WHERE GameID=$gameID";
    $results = $db->query($sql) or die (mysqli_error($db));
    $results = $results->fetch_array();
    if (isset($results)) return $results[0];
    else  {
        include "header.php";
        echo "<H1>Error</H1><h3> Your module ID is invalid.</h3>";
        include "footer.php";
        exit();
    }
}

function getQuizFromDir($quizDir)  {
    $xml = simplexml_load_file("$quizDir/content.xml");

    $sql = "";
    $hardStop = False;

    $json = json_encode($xml);
    $quizArray = json_decode($json,TRUE);


    $urlbase = "images";
    $quizid = "";
    if (isset($quizArray['URLBASE'])) $urlbase = $quizArray['URLBASE'];
    else $hardStop = True;
    if (isset($quizArray['ID'])) $quizid = $quizArray['ID'];
    else $hardStop = True;
    $quiz = new Quiz($quizid, $urlbase);

    if (isset($quizArray['TITLE']))  {
        $quiz->setTitle($quizArray['TITLE']);
    }
    if (isset($quizArray['AUTHOR']))  {
        $quiz->setAuthor($quizArray['AUTHOR']);
    }
    if (isset($quizArray['TIMER']))  {
        $quiz->setDefaultTimer(intval($quizArray['TIMER']));
    }
    if (isset($quizArray['NOCONFIRM']))  {
        $quiz->setConfirmStatus(False);
    } else  {
		$quiz->setConfirmStatus(True);
	}
    if (isset($quizArray['COVER']))  {
        $quiz->setCover($quizArray['COVER']);
    }
    if (isset($quizArray['NOTRACKMODULE']))  {
        $quiz->setDivertModule($quizArray['NOTRACKMODULE']);
    }


    foreach ($quizArray as $field=>$val) {

        if ($field == "QUESTION") {
            foreach ($val as $q)  {
                $questionText = getValue($q, "TEXT");
                $question = new Question($questionText);
                if (isset($q['FEEDBACK']))  {
                    $question->setFeedback(True);
					if (isset($q['NOTIMER']))  {
						$question->setTimer(0);
					} else if (isset($q['TIMER']))  {
						$question->setTimer(intval($q['TIMER']));
					} else  {
						$question->setTimer($quiz->getDefaultTimer());
					}
                }

                if (isset($q['REQUIRED']))  {
                    $question->setRequired(True);
                }
                $question->setRandomizeTag(getValue($q, "RANDOM"));

                foreach ($q as $field2=>$val2) {
                    if ($field2 == "ANSWER") {
                        if (isset($val2[0]))  {
                            foreach ($val2 as $a)  {
                                addAnswer($a, $question);
                            }
                        } else {
                            addAnswer($val2, $question);
                        }
                    }
                    else if ($field2 == "IMG")  { $question->addImage($val2); }
                    else if ($field2 == "ANSIMG")  { $question->addAnswerImage($val2); }
                }
                $quiz->addQuestion($question);
            }
        }
    }

    if ($hardStop)  return False;
    else return $quiz;
}

?>
