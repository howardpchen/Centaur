#include <stdio.h>
const char NORMAL[] = "<QUESTION>\n<NOTIMER/>\n<FEEDBACK/>\n<TEXT>%d) Please identify normal or abnormal.&lt;p&gt;</TEXT>\n<IMG>%d.jpg</IMG>\n<ANSIMG>%dA.jpg</ANSIMG>\n<ANSWER><TAG>NORMAL</TAG><TEXT>Normal</TEXT><CORRECT/></ANSWER>\n<ANSWER><TAG>NORMAL</TAG><TEXT>Abnormal</TEXT></ANSWER>\n</QUESTION>\n\n";

const char ABNORMAL[] = "<QUESTION>\n<NOTIMER/><FEEDBACK/>\n<TEXT>%d) Please identify normal or abnormal.&lt;p&gt;</TEXT>\n<IMG>%d.jpg</IMG>\n<ANSIMG>%dA.jpg</ANSIMG>\n<ANSWER><TAG>ABNORMAL</TAG><TEXT>Normal</TEXT></ANSWER>\n<ANSWER><TAG>ABNORMAL</TAG><TEXT>Abnormal</TEXT><CORRECT/></ANSWER>\n</QUESTION>\n\n";

const char HEADER[] = "<QUIZ>\n\n"
"<!-- The URL base where we can find the images.  Defaults to root of domain if not set. -->\n"
"<URLBASE>/centaur/alpha/images</URLBASE>\n"
"<NOTRACKMODULE>1102</NOTRACKMODULE>\n"
"<NOCONFIRM/> <!-- Feedback given immediately after clicking a choice -->\n"
"<TIMER>45</TIMER>\n"
"<ID>1102</ID> <!-- Pick a number up to 11 digits for ID. -->\n"
"<AUTHOR>author</AUTHOR>\n"
"<TITLE>Normal vs Abnormal Chest X-Ray.</TITLE>\n"
"<!--\n"
"    Cover sheet contains a short blurb about the survey/quiz that \n"
"    follows.  At the end of the cover sheet the user will be given\n"
"    a chance to log in, create an account, or continue as Guest. \n"
"    Relevant disclosures should go here.\n"
"\n"
"    Data will not be collected if the user chooses Guest.\n"
"\n"
"    HTML is supported.\n"
"-->\n\n"
"<COVER>\n"
"Welcome!\n"
"</COVER>\n\n";

const char FOOTER[] = 
"\n"
"<QUESTION>\n"
"<REQUIRED/>\n"
"<TEXT>How useful was this exercise for learning normal chest x-rays?</TEXT>\n"
"<ANSWER><TEXT>0 Counterproductive</TEXT></ANSWER>\n"
"<ANSWER><TEXT>1 Not useful</TEXT></ANSWER>\n"
"<ANSWER><TEXT>2 Slightly useful</TEXT></ANSWER>\n"
"<ANSWER><TEXT>3 Somewhat useful</TEXT></ANSWER>\n"
"<ANSWER><TEXT>4 Very useful</TEXT></ANSWER>\n"
"</QUESTION>\n"
"\n"
"<QUESTION>\n<TEXT>Please enter any other comments below.\n&lt;TEXTAREA name=\"comment\"&gt;&lt;/TEXTAREA&gt;</TEXT>\n"
"</QUESTION>\n"
"\n</QUIZ>";

/*
 * 1-48 normal
 * 49-73 nodule
 * 74-98 consolidation
 * 99-111 pneumothorax
 */

const int quiz[20] = {1, 2, 49, 74, 99, 3, 75, 4, 100, 50, 5, 6, 7, 98, 111, 8, 73, 72, 9, 10};

int main (void)
{

	int i;
	FILE *fp = fopen("content.xml","w+");
	char tmp[4096];

	if (fp != NULL)
	{
		fputs(HEADER, fp);
		for(i=0;i<20;i++)
		{
			if (quiz[i]<49)
				sprintf(tmp, NORMAL, i+1, quiz[i], quiz[i]);
			else
				sprintf(tmp, ABNORMAL, i+1, quiz[i], quiz[i]);
			fputs(tmp, fp);
		}
		/* uncomment below for all images, 1-111 */
		/*
		for(i=1;i<48;i++)
		{
			sprintf(tmp, NORMAL, i, i);
			fputs(tmp, fp);
		}
		for(i=49;i<111;i++)
		{
			sprintf(tmp, ABNORMAL, i, i);
			fputs(tmp, fp);
		}
		*/
		fputs(FOOTER, fp);

		fclose(fp);

	}

	return 0;
}
