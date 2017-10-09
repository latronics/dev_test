
<!doctype html>

<html>
<head>

<script src="http://labelwriter.com/software/dls/sdk/js/DYMO.Label.Framework.latest.js"
        type="text/javascript" charset="UTF-8"> </script>

<script>

(function()
{
    // called when the document completly loaded
    function onload()
    {
        // prints the label
        printButton.onclick = function()
        {
            try
            {
                // open label
                var labelXml = '<?xml version="1.0" encoding="utf-8"?>\
<DieCutLabel Version="8.0" Units="twips">\
  <PaperOrientation>Landscape</PaperOrientation>\
  <Id>ReturnAddress</Id>\
  <PaperName>30330 Return Address</PaperName>\
  <DrawCommands>\
    <RoundRectangle X="0" Y="0" Width="1080" Height="2880" Rx="180" Ry="180" />\
  </DrawCommands>\
  <ObjectInfo>\
    <BarcodeObject>\
      <Name>BCNBarcode</Name>\
      <ForeColor Alpha="255" Red="0" Green="0" Blue="0" />\
      <BackColor Alpha="0" Red="255" Green="255" Blue="255" />\
      <LinkedObjectName></LinkedObjectName>\
      <Rotation>Rotation0</Rotation>\
      <IsMirrored>False</IsMirrored>\
      <IsVariable>True</IsVariable>\
      <Text>016-1000</Text>\
      <Type>Code39</Type>\
      <Size>Small</Size>\
      <TextPosition>Top</TextPosition>\
      <TextFont Family="Arial" Size="8" Bold="False" Italic="False" Underline="False" Strikeout="False" />\
      <CheckSumFont Family="Arial" Size="8" Bold="False" Italic="False" Underline="False" Strikeout="False" />\
      <TextEmbedding>None</TextEmbedding>\
      <ECLevel>0</ECLevel>\
      <HorizontalAlignment>Center</HorizontalAlignment>\
      <QuietZonesPadding Left="0" Top="0" Right="0" Bottom="0" />\
    </BarcodeObject>\
    <Bounds X="461" Y="593" Width="1957" Height="405" />\
  </ObjectInfo>\
  <ObjectInfo>\
    <TextObject>\
      <Name>Title</Name>\
      <ForeColor Alpha="255" Red="0" Green="0" Blue="0" />\
      <BackColor Alpha="0" Red="255" Green="255" Blue="255" />\
      <LinkedObjectName></LinkedObjectName>\
      <Rotation>Rotation0</Rotation>\
      <IsMirrored>False</IsMirrored>\
      <IsVariable>False</IsVariable>\
      <HorizontalAlignment>Left</HorizontalAlignment>\
      <VerticalAlignment>Top</VerticalAlignment>\
      <TextFitMode>ShrinkToFit</TextFitMode>\
      <UseFullFontHeight>True</UseFullFontHeight>\
      <Verticalized>False</Verticalized>\
      <StyledText>\
        <Element>\
          <String>Title of Item goes here</String>\
          <Attributes>\
            <Font Family="Arial" Size="12" Bold="False" Italic="False" Underline="False" Strikeout="False" />\
            <ForeColor Alpha="255" Red="0" Green="0" Blue="0" />\
          </Attributes>\
        </Element>\
      </StyledText>\
    </TextObject>\
    <Bounds X="326" Y="135" Width="2347" Height="195" />\
  </ObjectInfo>\
  <ObjectInfo>\
    <TextObject>\
      <Name>Title2</Name>\
      <ForeColor Alpha="255" Red="0" Green="0" Blue="0" />\
      <BackColor Alpha="0" Red="255" Green="255" Blue="255" />\
      <LinkedObjectName></LinkedObjectName>\
      <Rotation>Rotation0</Rotation>\
      <IsMirrored>False</IsMirrored>\
      <IsVariable>False</IsVariable>\
      <HorizontalAlignment>Left</HorizontalAlignment>\
      <VerticalAlignment>Top</VerticalAlignment>\
      <TextFitMode>ShrinkToFit</TextFitMode>\
      <UseFullFontHeight>True</UseFullFontHeight>\
      <Verticalized>False</Verticalized>\
      <StyledText>\
        <Element>\
          <String>Specs of item go here</String>\
          <Attributes>\
            <Font Family="Arial" Size="12" Bold="False" Italic="False" Underline="False" Strikeout="False" />\
            <ForeColor Alpha="255" Red="0" Green="0" Blue="0" />\
          </Attributes>\
        </Element>\
      </StyledText>\
    </TextObject>\
    <Bounds X="326" Y="360" Width="2392" Height="195" />\
  </ObjectInfo>\
</DieCutLabel>';


                var label = dymo.label.framework.openLabelXml(labelXml);

                // set label text
                label.setObjectText("BCNBarcode", BCN.value);
                label.setObjectText("Title", Title.value);
                label.setObjectText("Title2", Title2.value);
                
                // select printer to print on
                // for simplicity sake just use the first LabelWriter printer
                var printers = dymo.label.framework.getPrinters();
                if (printers.length == 0)
                    throw "No DYMO printers are installed. Install DYMO printers.";

                var printerName = "";
                for (var i = 0; i < printers.length; ++i)
                {
                    var printer = printers[i];
                    if (printer.printerType == "LabelWriterPrinter")
                    {
                        printerName = printer.name;
                        break;
                    }
                }
                
                if (printerName == "")
                    throw "No LabelWriter printers found. Install LabelWriter printer";

                // finally print the label
                label.print(printerName);
            }
            catch(e)
            {
                alert(e.message || e);
            }
        }
    };

    // register onload event
    if (window.addEventListener)
        window.addEventListener("load", onload, false);
    else if (window.attachEvent)
        window.attachEvent("onload", onload);
    else
        window.onload = onload;

} ());

</script>
</head>
<body>

<input id = "BCN" size="6"></input>
<input id = "Title" size="6"></input>
<input id = "Title2" size="6"></input>

<button name='printbutton' id="printButton" >P</button>

</body>
</html>

