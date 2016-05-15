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
          <div class="row auto-size ">
            <div class="cell size-p100 padding20">
              <xsl:call-template name="content"/>
            </div>
          </div>
        </div>
        <script type="text/javascript">
          var path = '<xsl:value-of select="/data/path"/>';
          initCompare();
        </script>

        <xsl:call-template name="dialog"/>

      </body>
    </html>
  </xsl:template>

  <xsl:template name="content">

    <div class="tabcontrol2" data-role="tabcontrol">
      <ul class="tabs">
        <li>
          <a href="#history">History</a>
        </li>

        <li>
          <a href="#githistory">Git History</a>
        </li>
      </ul>
      <div class="frames">
        <div class="frame" id="history">
          <a href="{/data/system/baseUrl}{/data/system/requestUri}/delete/all" class="button danger">Delete Complete
            History
          </a>
          <xsl:call-template name="browser">
            <xsl:with-param name="browser" select="/data/history/*"/>
          </xsl:call-template>
        </div>
        <div class="frame" id="githistory">
          <xsl:call-template name="git-history"/>
        </div>
      </div>
    </div>

  </xsl:template>

  <xsl:template name="dialog">
    <div data-role="dialog" id="dialog" class="padding20 dialog" data-close-button="true" data-overlay="true"
         data-overlay-color="op-dark" data-windows-style="true"
         style="left: 0px; right: 0px; width: auto; height: auto; visibility: visible; top: 213px;">
      <h3>Diff</h3>
      <div id="dialog-content">
      </div>
    </div>
  </xsl:template>

  <xsl:template name="git-history">
    <table class="table border bordered">

      <xsl:for-each select="/data/githistory/item">
        <tr>
          <td>
            <xsl:value-of select="item[1]"/>
          </td>
          <td>
            <xsl:value-of select="item[2]"/>
          </td>
          <td>
            <xsl:value-of select="item[3]"/>
          </td>
          <td>
            <xsl:value-of select="item[4]"/>
          </td>

          <td>
            <a href="{/data/system/baseUrl}{/data/system/requestUri}/compare/{item[1]}" data-compare="{item[1]}"
               class="inactive">

              <span data-role="hint"
                    data-hint-background="bg-green"
                    data-hint-color="fg-white"
                    data-hint-mode="2"
                    data-hint="compare {item[1]}">
                <span class="mif-shrink2"></span>
              </span>

            </a>
            <a href="{/data/system/baseUrl}{/data/system/requestUri}/revert/{item[1]}" data-revert="{item[1]}">

              <span data-role="hint"
                    data-hint-background="bg-green"
                    data-hint-color="fg-white"
                    data-hint-mode="2"
                    data-hint="revert to {item[1]}">
                <span class="mif-backward"></span>
              </span>

            </a>

          </td>
        </tr>

      </xsl:for-each>

    </table>
  </xsl:template>


  <xsl:template name="browser">
    <xsl:param name="browser"/>

    <xsl:for-each select="$browser">
      <div class="panel" data-role="panel">
        <div class="heading">
          <span class="title">
            <xsl:value-of select="name($browser)"/>
            <a href="{/data/system/baseUrl}{/data/system/requestUri}/delete/{name($browser)}/10"
               class="button danger mini-button"
               style=" margin: 0 53px; position: absolute; right: 0; padding: 6px">
              delete 10 oldest entries
            </a>
          </span>
        </div>
        <div class="content">
          <xsl:call-template name="page">
            <xsl:with-param name="page" select="$browser/*"/>
          </xsl:call-template>
        </div>
      </div>
    </xsl:for-each>
  </xsl:template>

  <xsl:template name="page">
    <xsl:param name="page"/>
    <xsl:for-each select="$page">
      <div class="panel collapsed" style="padding:0 10px;" data-role="panel">
        <div class="heading">
          <span class="title">
            <xsl:choose>
              <xsl:when test="@name">
                <xsl:value-of select="@name"/>
              </xsl:when>
              <xsl:otherwise>
                <xsl:value-of select="name(.)"/>
              </xsl:otherwise>
            </xsl:choose>

          </span>
        </div>
        <div class="content">
          <xsl:call-template name="runs">
            <xsl:with-param name="run" select="item"/>
          </xsl:call-template>
        </div>
      </div>
    </xsl:for-each>
  </xsl:template>

  <xsl:template name="runs">
    <xsl:param name="run"/>
    <xsl:for-each select="$run">
      <xsl:variable name="cssClass">
        <xsl:choose>
          <xsl:when test="$run/result = '1'">success</xsl:when>
          <xsl:otherwise>alert</xsl:otherwise>
        </xsl:choose>
      </xsl:variable>

      <div class="panel collapsed {$cssClass}" data-role="panel">
        <div class="heading">
          <span class="title">
            <xsl:value-of select="date"/><xsl:text> </xsl:text>
            <xsl:value-of select="time"/>
          </span>
        </div>
        <div class="content" style="margin:0 10px;">
          <xsl:call-template name="command-list">
            <xsl:with-param name="run" select="run"/>
          </xsl:call-template>
        </div>
      </div>
    </xsl:for-each>
  </xsl:template>


  <xsl:template name="command-list">
    <xsl:param name="run"/>
    <table class="table no-margin">
      <xsl:for-each select="$run/item">
        <xsl:variable name="cssClass">
          <xsl:choose>
            <xsl:when test="item[1] = '1'">success</xsl:when>
            <xsl:otherwise>error</xsl:otherwise>
          </xsl:choose>
        </xsl:variable>
        <tr class="{$cssClass}">
          <td>
            <xsl:value-of select="item[2]"/>
          </td>
          <td>
            <xsl:value-of select="item[3]"/>
          </td>
        </tr>
      </xsl:for-each>
    </table>
  </xsl:template>


</xsl:stylesheet>