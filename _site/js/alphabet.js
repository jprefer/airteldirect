// Generates a line of alphabet hyperlinks to be used in text index.
// Serves merely as a shorthand to save typing as well as page loading
// time by eliminating massive HTML repetitions.

// Function arguments (optional):
// 1. Alphabet letter to be skipped (Default: none skipped)
//    Note: Should be specified at least as an empty placeholder when
//          the second argument is necessary.
// 2. Alphabet letters to be deactivated as links (Default: all active)

// The letter to be skipped won't show in the generated alphabet line
// (used when alphabet line is for the links from this letter).

// The letters to be deactivated as links will be shown as passive (gray)
// placeholders on the generated alphabet line in contrast with the other
// letters shown as active links to the letters of the index. This might
// be necessary for the letters that are not presented in the index.

// The returned value is the HTML for hyperlinked alphabet line to be used
// in the "document.write".

   function ALPHABET()
            {Skip=""
             if (arguments.length>0)
                Skip=arguments[0]
             Inactive=""
             if (arguments.length>1)
                Inactive=arguments[1]
             Alphabet  ="ABCDEFGHIJKLMNOPQRSTUVWXYZ"
             Alpha_Line=""
             for (i=0;i<26;i++)
                 {Letter=Alphabet.substr(i,1)
                  if ((Alpha_Line!="" )&&  // Suppress ending "|" on the sides
                      (!((Skip   =="Z")&&
                         (Letter =="Z"))))
                     Alpha_Line=Alpha_Line+"<FONT COLOR=Green>|</FONT>"
                  if (Letter!=Skip)  // Suppress "Skip" letter (if any)
                     if (Inactive.indexOf(Letter)==-1)  // Letter is active
                        Alpha_Line=Alpha_Line+" <A HREF='showrates.php?letter="+Letter+"&card="+name+"' target='rates_frm'>"+Letter+"</A>"
                     else  // Letter is inactive
                        Alpha_Line=Alpha_Line+" <FONT COLOR=Gray>"+Letter+"</FONT>"
                  if ((Letter!="Z")&&  // Suppress extra blanks
                      (Letter!=Skip))
                     Alpha_Line=Alpha_Line+" "
                 }
             return Alpha_Line
            }
//-->

