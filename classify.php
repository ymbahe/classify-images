<html>
<body>

<!-- CSS stylesheet for minimally less atrocious layout... -->
<link rel="stylesheet" type="text/css" href="classify.css" />
   
<?php

/* This function writes the collected data to an ASCII file.
 *
 * @param $file : The file handle to write to
 * @param $currim : Index of the current image
 */
function writeData($index) {

  /* Retrieve responses from previous iteration's form */
  $datf=$_POST["datfpass"];
  $score=$_POST["score"];
  $special=$_POST["special"];
  $comment=$_POST["comment"];

  /* Only write result if main question has been answered */
  if (isset($score)) {
    
    if (!isset($special))
      $special = 0;
  
    if ($comment == '')
      $comment='-';

    /* Report answer on screen to enable checking */
    echo "<br>";
    echo "Last image: " . strval($index) . ", answer: " . $score;
    echo "<br>";

    $file=fopen("./doc/" . $datf, "a+") or die($php_errormsg);

    /* Write header if this is the first entry */
    if ($index == 1)
      fwrite($file, "## Galaxy  Score  Special? Comment \n");

    /* Write actual result for PREVIOUS image (hence "-1") */    
    fwrite($file, $index . "   " . $score . "   " . $special . "   " .
      $comment ."\n"); 

    fclose($file);

  } /* ends section if main question was answered */
} /* ends writeData() */


/* -------------------------
 * ACTUAL SCRIPT STARTS HERE
 * -------------------------
 */

/* First check if we are still setting up... */

/* Load back image number from last iteration */
$preim = $_POST["imagenum"];

/* No images processed yet? Then we have some set up to do */
if (!$preim) {
          
  /* Load back the output data file. If it has not been passed through
   * from the last iteration, we are at the very first setup stage */
  $datf= $_POST["datafile"];
  if (!$datf) {

    /* Form asking for data file is plain HTML, temporarily exist PHP */    
    ?> <h3>Welcome to the Galaxy classification script!</h3>
    <p>Please specify the output data file:</p>
    
    <form method="post">
      <input type="text" name="datafile" placeholder="Output file">
      <input type="submit" name="submdf" value="Start">
    </form>
    
    <!-- Nothing more to do in this iteration, jump to the end... -->
    <?php goto endofscript;
  
  } else {
    
    /* Ok, so we HAVE previously specified the output file name. */       
    
    echo "<p> Initialising the first pass... ";
    
    /* There are two options:
     * (i) We are beginning a brand new analysis run (no output file yet)
     * (ii) We are resuming an interrupted run (output file does already exist)
     */
    if (file_exists("./doc/" . $datf)) {

      /* Ok, we're in option ii. Get the last line of the output file. */
      $file=fopen("./doc/" . $datf, "r");
      while(!feof($file)) {
        $oldline=$line;
        $line=fgets($file);
      }
      fclose($file);
      echo "Last line in data file: " . $oldline . "<br>";
  
      /* Now extract the galaxy number -- first number in this line */
      do {
        $char1=substr($oldline, $ichar, 1);
        $fullchar=$fullchar . $char1;
        $ichar++;
      } while (is_numeric($char1));

      $currim=$fullchar+1;
      echo "Last image number: $fullchar, continuing from galaxy $currim.<br><br>";
           
    } else {  

      /* Option i is easier, just start from image 1 */
      $currim=1;
      echo "Starting from the beginning with galaxy " . $currim ."<br><br>";
    }
  } /* ends post-intro-setup section */

/* ============== ENDS INITIALIZATION SECTION ====================== */

} else { 

  /* Ok, we HAVE previously processed an image, so we just keep going */

  /* Sanity check: have we answered the main question in the last round? */
  $fateresp=$_POST["score"];
  $datf=$_POST["datfpass"];

  /* 0 means 'no answer', so write a friendly admonition. 
   * Don't increase the image number, so we'll see the same image as last
   * time again.
   */        
  if ($fateresp == 0) {
    echo "<br>Please make a CHOICE, for UL's sake.<br>";
    $currim=$preim;
  } else {
    $currim=$preim+1;
    writeData($preim);
  }

} /* Ends we-have-previously-classified section */

/* Rest is the same for all situations, except for output setup
 * (in which case we skip all this anyway) */        

echo "<h1> Galaxy $currim </h1>";
      
/* Getting close to doing useful stuff. First, set up image file name */
$im="./classifyimages/image_" . $currim . ".png";
   
/* Check if this image file exists */
if (!file_exists($im)) {

  echo "Image file $im does not exist.<br>";
  echo "Either the end is reached, or more images need to be created!<br>";
   
} else {

  /* Current image file DOES exist -- display it */
  echo "<img src=\"$im\" style=\"max-height: 600px; max-width: 90%\">";
      
  /* Now comes the answer form, which is plain HTML */  
  ?> <form method="post">

    <div id="textbox">

      <!-- Need two 'hidden' elements to pass image number and result file
           to next iteration --> 
      <input type="hidden" name="imagenum" value="<?=$currim?>">
      <input type="hidden" name="datfpass" value="<?=$datf?>">

    <p class="alignleft">
      Jellyfish appearance:   
      <input type="radio" name="score" value=1> No
      <input type="radio" name="score" value=2> Maybe
      <input type="radio" name="score" value=3> Yes      .
    </p>   

    <p class="alignright"> 
      <input type="text" name="comment" placeholder="Any comments?">
      <input type="checkbox" name="special" value="1"> Special?
      <input type="submit" name="next" value="Next galaxy">
    </p>

    </div>

  </form>

  <!-- Almost done... -->
  <?php 

} /* Ends section if current image does exist */

endofscript:
?>
  
</body>
</html>
