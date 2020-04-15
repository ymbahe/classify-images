<html>
<body>

<!-- CSS stylesheet for minimally less atrocious layout... -->
<link rel="stylesheet" type="text/css" href="classify.css" />
   
<h1>Galaxy classification</h1>
<?php

/* This function writes the collected data to an ASCII file.
 *
 * @param $file : The file handle to write to
 * @param $currim : Index of the current image
 */
function writeData($file, $currim) {

  /* Retrieve responses from previous iteration's form */
  $fate=$_POST["fate"];
  $fate2=$_POST["fate2"];
  $offdisc=$_POST["offdisc"];
  $unsure=$_POST["unsure"];
  $prefhot=$_POST["prefhot"];
  $tail=$_POST["tail"];
  $blobnum=$_POST["blobnum"];
  $interesting=$_POST["interesting"];
  $comment=$_POST["comment"];

  /* Only write result if main question has been answered */
  if (isset($fate)) {

    if (!isset($fate2))
      $fate2 = 0;

    if (!isset($offdisc))
      $offdisc = 0;
 
    if (!isset($unsure))
      $unsure = 0;

    if (!isset($prefhot))
      $prefhot = 0;
   
    if (!isset($blobnum))
      $blobnum = 0;

    if (!isset($tail))
      $tail = 0;
    
    if (!isset($interesting))
      $interesting = 0;
  
    /* Report answer on screen to enable checking */
    echo "Last answer: " . $fate;
    echo "<br><br>";

    /* Write header if this is the first entry */
    if ($currim-1 == 1)
      fwrite($file, "## Galaxy  Fate1  Fate2 OffDisc  Unsure  PrefHot  Tail? BlobNum  Interesting?  Comment \n");

    /* Write actual result for PREVIOUS image (hence "-1") */    
    fwrite($file, $currim-1 . "   " . $fate . "   " . $fate2 . "   " . $offdisc . "   " . $unsure . "   " . $prefhot . "   " . $tail . "   " . $blobnum . "   " . $interesting . "   " . $comment ."\n");
  }
} /* ends writeData() */


/* -------------------------
 * ACTUAL SCRIPT STARTS HERE
 * -------------------------
 */

/* First check if we are still setting up... */
$preim = $_POST["imagenum"];

if (!$preim) {
          
        // Is this the very first time it's been called?
        $datf= $_POST["datafile"];
        if (!$datf) {
          ?>      
    
          <h3>Welcome to the galaxy classification site!</h3>
          <p>Please specify data file:</p>
    
          <form method="post">
            <input type="text" name="datafile">
            <input type="submit" name="submdf" value="Start">
          </form>
    
          <?php
          goto endofscript;
        } else {
          ?>

          <p> Initialising the first run...</p>
    
          <?php
 
          // Let's make sure the file exists at all...
          if (file_exists("./doc/" . $datf)) {
            $f1=fopen("./doc/" . $datf, "r");
            while(!feof($f1)) {
              $oldcl=$cl;
              $cl=fgets($f1);
            }
  
            echo "Last line of data file: " . $oldcl . "<br>";
  
            // Now extract character by character until white space...
  
            do {
              $char1=substr($oldcl, $ichar, 1);
              $fullchar=$fullchar . $char1;
              $ichar++;   
            } while (is_numeric($char1));
  
            echo "Last Number: " . $fullchar . "<br>";
            $currim=$fullchar+1;
            echo "Continuing from galaxy " . $currim . "<br><br>";
            fclose($f1);
          } else {  // end of section if output file EXISTS

            echo "Input file does not yet exist.<br>";
            $currim=1;
            echo "Starting from the beginning with galaxy " . $currim ."<br><br>";
          }
        }
      } else { //  end of initialisation section

        //    echo "Welcome back! <br><br>";
        //    echo "Attempting to read back datafile...<br>";
        $datf= $_POST["datfpass"];
        //    echo "... done: " . $datf . " <br>";
        $fateresp = $_POST["fate"];
        if ($fateresp == 0) {
          $currim = $_POST["imagenum"];
          echo "Please make a CHOICE!!!<br>";
        } else {
          $currim = $_POST["imagenum"]+1;
        }
        //    echo "Updated currim: " . $currim . "<br><br>";
      }
    
      //    echo "Opening output file...<br>";
      //    echo $datf . "<br>";
    
      $file=fopen("./doc/" . $datf, "a+");
      //    echo "  ...done!\n";
    
      ?>
      <h3> Galaxy <?php echo $currim ?> </h3>
      <?php
      
      // Now we can finally do the proper stuff. First, set up file name:
      $im="./classifyimages/test" . $currim .".png";
   
      // Check if this image file exists:
      if (!file_exists($im)) {

        echo "Image file $im does not exist.<br>";
        echo "Either end is reached, or more images need to be created!<br>";
   
        // Still need to write answer from LAST galaxy!!
      
        writeData($file, $currim);

        /*

        $fate=$_POST["fate"];
        $fate2=$_POST["fate2"];
      
        $offdisc=$_POST["offdisc"];
        $unsure=$_POST["unsure"];
        $prefhot=$_POST["prefhot"];
      
        $tail=$_POST["tail"];
        $blobnum=$_POST["blobnum"];
        $interesting=$_POST["interesting"];

        $comment=$_POST["comment"];

        if (isset($fate)) {

          if (!isset($fate2))
            $fate2 = 0;

          if (!isset($offdisc))
            $offdisc = 0;
       
          if (!isset($unsure))
            $unsure = 0;
      
          if (!isset($prefhot))
            $prefhot = 0;
         
          if (!isset($numblob))
            $numblob = 0;
      
          if (!isset($tail))
            $tail = 0;
          
          if (!isset($interesting))
            $interesting = 0;
        
          echo "Last answer: " . $fate;
          echo "<br><br>";
     
          if ($currim-1 == 1)
            fwrite($file, "## Galaxy  Fate1  Fate2 OffDisc  Unsure  PrefHot  Tail? BlobNum  Interesting?  Comment \n");
          
          fwrite($file, $currim-1 . "   " . $fate . "   " . $fate2 . "   " . $offdisc . "   " . $unsure . "   " . $prefhot . "   " . $tail . "   " . $blobnum . "   " . $interesting . "   " .$comment ."\n");
          
        
          echo "Last number: " . strval($currim-1);    
          */

          fclose($file); 
        // }

      } else {
    
        echo "<img src=\"$im\">";
      
        ?>
   
        <form method="post">

          <div id="textbox">
          <p class="alignleft"> 
            <input type="hidden" name="imagenum" value="<?=$currim?>">
            <input type="hidden" name="datfpass" value="<?=$datf?>">
            <input type="radio" name="fate" value=1> Strangulation
            <input type="radio" name="fate" value=2> Ram pressure
            <input type="radio" name="fate" value=3> Tidal      .
            <input type="text" name="comment" placeholder="Any comments?">
          </p>   
    
          <p class="alignright"> 
            <input type="checkbox" name="offdisc" value="1"> Offset disc
            <input type="checkbox" name="unsure" value="1"> Unsure?
            <input type="checkbox" name="prefhot" value="1"> Hot stripping?
          </p>

          </div>
          
          <div style="clear: both;"></div>
    
          <div id="textbox">
        
          <p class="alignleft">
            <input type="radio" name="fate2" value=1> Strangulation
            <input type="radio" name="fate2" value=2> Ram pressure
            <input type="radio" name="fate2" value=3> Tidal     .   
            Second choice
          </p>

          <p class="alignright">
            <input type="text" name="blobnum" placeholder="#Blobs">
            <input type="checkbox" name="tail" value="1"> Tail?
            <input type="checkbox" name="interesting" value="1"> Special?
            <input type="submit" name="next" value="Next galaxy">
          </p>  

          </div>
      
          <div style="clear: both;"></div>

        </form>
   
        <?php 
  
        //     echo "Current galaxy: " . $currim;

       writeData($file, $currim);

       /*
       $fate=$_POST["fate"];
       $fate2=$_POST["fate2"];
       
       $offdisc=$_POST["offdisc"];
       $unsure=$_POST["unsure"];
       $prefhot=$_POST["prefhot"];

       $numblob=$_POST["numblob"];
       $tail=$_POST["tail"];
       $interesting=$_POST["interesting"];

       $comment=$_POST["comment"];

       if (isset($fate)) {

         if (!isset($fate2))
           $fate2 = 0;
       
         if (!isset($offdisc))
           $offdisc = 0;
      
         if (!isset($unsure))
           $unsure = 0;
       
         if (!isset($prefhot))
           $prefhot = 0;
       
         if (!isset($numblob))
           $numblob = 0;
       
         if (!isset($tail))
           $tail = 0;
       
         if (!isset($interesting))
           $interesting = 0;

         //     if (!isset($comment))
         //     $comment =  '.-.' ;
     
       echo "Last answer: " . $fate;
       echo "<br><br>";

       if ($currim-1 == 1)
         fwrite($file, "## Galaxy  Fate1  Fate2 OffDisc  Unsure  PrefHot  Tail? BlobNum  Interesting?  Comment \n");
   
       fwrite($file, $currim-1 . "   " . $fate . "   " . $fate2 . "   " . $offdisc . "   " . $unsure . "   " . $prefhot . "   " . $tail . "   " . $blobnum . "   " . $interesting . "   " .$comment ."\n");

       echo "Last number: " . strval($currim-1);

       */

       fclose($file);
     }

     endofscript:
     ?>

  
</body>
</html>
