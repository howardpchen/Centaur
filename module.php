<?php 
/*
    Copyright 2014 Po-Hao Chen.

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
include "primerLib.php";

$moduleID = $_GET['moduleid'];
$quizDir = getDirFromID($moduleID);
$quiz = getQuizFromDir($quizDir);
$quiz->randomizeAll($quiz);
if (!isset($_GET['moduleid']))  {
    header("location:/centaur/");
}
else if ($quiz->getCover() && !isset($_SESSION['username'])) {
    header("location:/centaur/cover.php?moduleid=" . $_GET['moduleid']);
}

include "header.php";

?>
<script>
var numA = [];
var feedback = [];
var timeLimit = [];
var answers = [];
var answertags = [];
var answerFields = [];
var currentQuestion = 0;
var currentSelection = -1;
var confirmStatus = <?php if ($quiz->getConfirmStatus()) echo "true"; else echo "false"; ?>


// Adapted from MDC for indexOf function used for <IE9
if (!Array.prototype.indexOf) {
	Array.prototype.indexOf = function(elt /*, from*/) {
		var len = this.length >>> 0;
		var from = Number(arguments[1]) || 0;
		from = (from < 0)
			? Math.ceil(from)
			: Math.floor(from);
		if (from < 0) from += len;

		for (; from < len; from++) {
			if (from in this && this[from] === elt) return from;
		}
		return -1;
	};
}
<?php
$numq = sizeof($quiz->getQuestions());
foreach ($quiz->getQuestions() as $ques)  {
    $numa = sizeof($ques->getAnswerChoices());
    echo "numA.push($numa);\n";
    if ($ques->hasFeedback()) {
        echo "feedback.push(true);\n";
    } else echo "feedback.push(false);\n";
    $answer = $ques->getAnswerPoints();
    $answer = join(", ", $answer);
    if (!$answer) echo "answers.push([-1]);\n";
    else echo "answers.push(new Array($answer));\n";
	echo "timeLimit.push(" . $ques->getTimer() . ")\n";

    $tagList = array();
    foreach ($ques->getTags() as $tag) {
        foreach ($ques->getAnswersByTag($tag) as $temp) {
            $tagList []= "'$tag'";
        }
    }
    $tagList = join(", ", $tagList);
    echo "answertags.push(new Array($tagList));\n";
}
?>
function loadHiddenFields()  {
    for (var i = 0; i < numA.length; i++)  {
        answerFields[i] = [];
        var tags_unique = [];
        for (var j = 0; j < numA[i]; j++)  {
            if (tags_unique.indexOf(answertags[i][j]) < 0) {
                answerFields[i].push(document.getElementById("answer_"+i+"_" + answertags[i][j]));
                tags_unique.push(answertags[i][j]);
            }
        }
    }
}

function runFeedback(qindex, aindex, tag)  {
	$('#centaurTimer').countdown('pause');
	timeLimit[currentQuestion] = 0;
    for (var i = 0; i < numA[qindex]; i++)  { 
        var theElement = document.getElementById('q' + qindex + 'a' + i);
        if (theElement.className == "button selected") {
            if (answers[qindex][i] > 0)  {
                theElement.className = (answers[qindex][i] >= 1) ? 'button right selected' : 'button right half selected';
            } else  {
                theElement.className = 'button wrong selected';
            }
        }
        else if (answers[qindex][i] > 0) {
                theElement.className = (answers[qindex][i] >= 1) ? 'button right' : 'button right half';
        } else  {
            theElement.className='button fade';
        }
        theElement.onclick = null;
    }
	if ($('#container' + qindex + ' div.answerImage').length)  {
		$('#container' + qindex + ' div.questionImage').hide();
		$('#container' + qindex + ' div.answerImage').show();
	}
	if (!($.browser.msie && $.browser.version == 8)) {
		scoreToDisplay = 0;
		for (var i = 0; i < numA[qindex]; i++)  { 
			if (document.getElementById('q' + qindex + 'a' + i).className.indexOf("selected") >= 0)  {
				scoreToDisplay += answers[qindex][i];
			};
		}
		$('#scorePanel').text("+" + scoreToDisplay);

		if (scoreToDisplay == 0)  {
			$('#scorePanel').attr('style', 'color:#f77');
		} else if (scoreToDisplay < 1)  {
			$('#scorePanel').attr('style', 'color:#ff7');
		} else $('#scorePanel').attr('style', 'color:#7f7');

		$('#scorePanel').animate({
			opacity: 0,
			fontSize: "5em",
			borderWidth: "0px"
		}, 1500);
		$('#scorePanel').hide(10);
	}
}
function recheckAnswers(qindex, aindex, tag, selectedText) {
    for (var i = 0; i < numA[qindex]; i++)  { 
        if (i == aindex) document.getElementById('q' + qindex + 'a' + i).className='button selected';
        else if (answertags[qindex][i] == tag) {
            document.getElementById('q' + qindex + 'a' + i).className='button';
        }
    }
    document.getElementById("answer_"+qindex+"_"+tag).value = selectedText;
    allselected = true;
    for (var i = 0; i < answerFields[qindex].length; i++)  {
        if (answerFields[qindex][i].value == "NOANSWER") allselected = false;
    }
    if (allselected) {
        if (document.getElementById("btn_conf"+qindex) && confirmStatus)  {
            document.getElementById("btn_conf"+qindex).className = "btnConfirm"; 
        } else  {
            if (qindex < numA.length-1) enableNext();
            else document.getElementById("btn_next"+qindex).className = "btnSubmit";
        }

    }
    if (!feedback[qindex])  {
        enableNext();
    } else  {
		if (!confirmStatus)  {
			// Auto confirm if NOCONFIRM is on.
			runFeedback(qindex, aindex, tag);
			enableNext();
			return;
		}
	}
    $('a#btn_conf'+qindex).off("click").click(function(){
		currentSelection = aindex;
        if (document.getElementById("btn_conf"+qindex).className != "btnConfirm") return; 
		runFeedback(qindex, aindex, tag);
        document.getElementById("btn_conf"+qindex).className = "btnNextDisabled";
        if (qindex < numA.length-1) enableNext();
        else document.getElementById("btn_next"+qindex).className = "btnSubmit";

        $('.btnSubmit').off("click").click(function(){
            var el = $('#progress');
            el.width(el.width() + 800/numQ + 'px');
			document.getElementById('quizform').submit();
			$('.btnSubmit').off("click").switchClass("btnSubmit", "btnNextDisabled");

            });
    });

    $('.btnSubmit').off("click").click(function(){
        var el = $('#progress');
        el.width(el.width() + 800/numQ + 'px');
        document.getElementById('quizform').submit();
		$('.btnSubmit').off("click").switchClass("btnSubmit", "btnNextDisabled");
    });


}
function timesUp()  {
	document.getElementById('centaurTimer').className = 'button wrong';
	document.getElementById('btn_conf'+currentQuestion).className = 'btnNextDisabled';

	runFeedback(currentQuestion, currentSelection);
	enableNext();
}

function timerWarning(periods)  {
	if ($.countdown.periodsToSeconds(periods) === 5) { 
        $('#centaurTimer').addClass('right half');
    }
}

function recheckTimerStatus()  {
	if (timeLimit[currentQuestion] > 0)  {
		$('#centaurTimer').countdown({layout: '{sn} {sl}', until:+timeLimit[currentQuestion], onExpiry: timesUp, onTick: timerWarning, format: 'S'});
		$('#centaurTimer').countdown('resume');
		$('#centaurTimer').countdown('option', {until:+timeLimit[currentQuestion]});
	} else  {
		$('#centaurTimer').countdown('pause');	
		$('#centaurTimer').text("Timer Off");
	}
}


function setNextButton() {
	$('.btnNext').off('click');
	$('.btnNext').click(function(){
			currentQuestion++;
			currentSelection = -1;
			document.getElementById('centaurTimer').className = 'button';
			$(this).parents('.questionContainer').fadeOut(250, function(){
				$(this).next().fadeIn(250, recheckTimerStatus);
				var id = $(this).attr('id');
				var count = id.substr(9);	
				var newID = 'container'+(parseInt(count)+1);
				document.getElementById(newID).style.display="block";
				// Use AJAX to send current results to be saved.
				/*
				   $.ajax({
					type: "POST",
					url: "submit.php?question=" + currentQuestion,
					data: $("#quizform").serialize(), // serializes the form's elements.
					success: function(data)
					{
					alert(data);
				//Do nothing - just automatically saving the input.

				}
				});
				 */
				});
			var el = $('#progress');
			el.width(el.width() + 800/numQ + 'px');
			}); 
}
function enableNext ()  {
    for (var i = 0; i < numA.length-1; i++)  {
        if ($("a#btn_next"+i).is(":visible"))  {
            document.getElementById("btn_next"+i).className = "btnNext";
        }
    }
    if ($("a#btn_next"+(numA.length-1)).is(":visible"))  {
        document.getElementById("btn_next"+(numA.length-1)).className = "btnSubmit";
        $('.btnSubmit').off("click").click(function(){
            var el = $('#progress');
            el.width(el.width() + 800/numQ + 'px');
            document.getElementById('quizform').submit();
			$('.btnSubmit').off("click").switchClass("btnSubmit", "btnNextDisabled");

        });
    }
	setNextButton();
    
}


function form_params( form )
{
    var params = new Array()
    var length = form.elements.length
    for( var i = 0; i < length; i++ )
    {
    	element = form.elements[i]

    	if(element.tagName == 'TEXTAREA' )
    	{
    		params[element.name] = element.value
    	}
    	else if( element.tagName == 'INPUT' )
    	{
    		if( element.type == 'text' || element.type == 'hidden' || element.type == 'password')
    		{
    			params[element.name] = element.value
    		}
    		else if( element.type == 'radio' && element.checked )
    		{
    			if( !element.value )
    				params[element.name] = "on"
    			else
    				params[element.name] = element.value

    		}
    		else if( element.type == 'checkbox' && element.checked )
    		{
    			if( !element.value )
    				params[element.name] = "on"
    			else
    				params[element.name] = element.value
    		}
    	}
    }
    return params;
}

</script>

<div id="scorePanel" class="score"></div>
<div id="timerPanel">
<p>
 <!--<a class="button" id="openTimer" onClick="$('#centaurTimer').fadeToggle(200)">&gt;</a>-->
<label style="font-size:8pt">Drag to Move</label><br>
<a class="button" id="centaurTimer">Timer Off</a>
</div>

<h1><?php $quiz->getTitle(); ?></h1>
<form onkeypress="return event.keyCode!=13" action='submit.php' method='post' id='quizform'>
<input type="hidden" name="moduleid" value="<?php echo $moduleID;?>">
<input type="hidden" name="attemptID" value="<?php echo time();?>">
<?php
$qnum = 0;
foreach ($quiz->getQuestions() as $ques) {
    $imgFiles = $ques->getImages();
    $ansImgFiles = $ques->getAnswerImages();
    $imgString = "";
    $ansImgString = "";
    $qid = $ques->getID();

    foreach ($imgFiles as $f)  {
        if (sizeof($f) == 1)  {
            $imgString .= "<IMG SRC='" . $quiz->getURL() . "/$f'> ";
        } else  {
            foreach ($f as $f1)  {
                $imgString .= "<IMG SRC='" . $quiz->getURL() . "/$f1'> ";
            }
        }
    }
    foreach ($ansImgFiles as $f)  {
        if (sizeof($f) == 1)  {
            $ansImgString .= "<IMG SRC='" . $quiz->getURL() . "/$f'> ";
        } else  {
            foreach ($f as $f1)  {
                $ansImgString .= "<IMG SRC='" . $quiz->getURL() . "/$f1'> ";
            }
        }
    }
    
    if ($qnum == 0) $hide = "";
    else $hide = "hide";
    $qtext = $ques->getQuestionText();
    echo <<< END

<div class="questionContainer radius $hide" id="container$qnum">
    <div class="question">

        <div class='questionImage'>$imgString</div>
END;

        if ($ansImgString) echo "<div class='answerImage hide'>$ansImgString</div>";

    echo <<< END
        $qtext
    </div>
    <center>
    <div class="answers" style="display:table-row">
END;
    $tags = $ques->getTags();
    $actualID = $ques->getID();
    $anum = 0;
    foreach ($tags as $tag) {
        echo "<div id='$tag' style='display:table-cell'>\n";
        $answersByTag = $ques->getAnswersByTag($tag);

        foreach ($answersByTag as $ans)  {
            $ansText = $ans->getAnswerText();
            echo "<a class='button' onClick=\"recheckAnswers($qnum,$anum, '$tag', '$ansText')\" id='q$qnum" . "a" . "$anum'>$ansText</A><BR>\n";
            $anum++;
        }
        echo "</div>\n";
        echo "<input type='hidden' name='actualQID_$actualID" . "_" . "answer_$qnum" . "_" . "$tag' id='answer_$qnum" . "_" . "$tag' value='NOANSWER' />\n";
    }
    echo <<< END
    </div>
    <div class="btnContainer">
        <div class="next">
END;

    if ($ques->isRequired() || ($qnum < $numq-1 && sizeof($ques->getAnswerChoices()) > 0)){
        if (!$ques->hasFeedback())  {
            if ($qnum == $numq-1) echo '<a class="btnNextDisabled" id="btn_next' . $qnum . '">Finish!</a>';
            else echo '<a class="btnNextDisabled" id="btn_next' . $qnum . '">Next &gt;&gt;</a>';
        }
        else  {
            echo '<a class="btnConfirmDisabled" id="btn_conf' . $qnum . '">Confirm</a><a class="btnNextDisabled" id="btn_next' . $qnum . '">Next &gt;&gt;</a>';
        }
    }
    else if ($qnum < $numq-1 && sizeof($ques->getAnswerChoices()) == 0) echo '<a class="btnNext" id="btn_next' . $qnum . '">Next &gt;&gt;</a>';
    else  {
        echo '<a class="btnSubmit" id="btn_next' . $qnum . '">Finish!</a>';
    }

    echo <<< END
            
        </div>
        <div class="prev">
END;

    //if ($qnum > 0) echo '<a class="btnPrev">&lt;&lt; Prev</a>';
   
    echo <<< END
        
        </div>
        <div class="clear"></div>
    </div>
    </center>
    </div>
</div>
END;
    $qnum++;
}

?>
</form>
<div align=center style="position:relative">
<div id="scoreKeeper" class="scores">
   <div align=left id="progressKeeper" class="radius">
<div id="progress"></div>
</div>

<center>


<SCRIPT>


var numQ = <?php echo $numq; ?>;
$(function(){
	$("input + label").on("click", function() {
		$("#" + $(this).parents("label").attr("for")).click();
		});
    var jQuiz = {
        init: function(){
			setNextButton();
            $('.btnPrev').click(function(){
				currentQuestion--;
				recheckTimerStatus();
                $(this).parents('.questionContainer').fadeOut(250, function(){
                    $(this).prev().fadeIn(250);
					var id = $(this).attr('id');
					var count = id.substr(9);	
					var newID = 'container'+(parseInt(count)-1);
					document.getElementById(newID).style.display="block";
                });
                var el = $('#progress');
                el.width(el.width() - 800/numQ + 'px');
            });
            $('.btnSubmit').click(function(){
                var el = $('#progress');
                el.width(el.width() + 800/numQ + 'px');
                document.getElementById('quizform').submit();
				$('.btnSubmit').off("click").switchClass("btnSubmit", "btnNextDisabled");

            });

        }
    };
    jQuiz.init();
	$("#timerPanel").draggable();
})


loadHiddenFields();

</SCRIPT>


<?php include "footer.php" ?>
