<xsl:stylesheet
        version="1.0"
        xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
>

  <xsl:import href="base.xsl"/>

  <xsl:template match="/">
    <xsl:text disable-output-escaping='yes'>&lt;!DOCTYPE html&gt;</xsl:text>
    <html>
      <xsl:call-template name="head"/>
      <body>

        <div class="flex-grid">
          <div class="row">
            <xsl:call-template name="application-bar"/>
          </div>
          <div class="row">
            <xsl:call-template name="breadcrumb"/>
          </div>
          <div class="row">
            <div class="cell colspan2">
              <xsl:call-template name="toc"/>
            </div>
            <div class="cell colspan10">
              <xsl:call-template name="content"/>
            </div>
          </div>
        </div>
      </body>
    </html>
  </xsl:template>

  <xsl:template name="content">
    <form action="{/data/system/baseUrl}/import/{/data/path}"
          method="post" >

    <input id="uploadFile"
           placeholder="Choose File"
           disabled="disabled"/>
    <br/><br/>

    <div class="fileUpload btn btn-primary">

    </div>
    <input type="submit"
           name="Upload File"
           class="btn uploadbtn"/>
  </form>
  </xsl:template>


</xsl:stylesheet>