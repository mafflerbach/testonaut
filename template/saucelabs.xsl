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
            <div class="cell colspan12 padding20">
              <xsl:call-template name="content"/>
            </div>
          </div>
        </div>
        <script type="text/javascript">

        </script>
      </body>
    </html>
  </xsl:template>

  <xsl:template name="content">
    <form action="" method="post">
      <div class="tabcontrol2" data-role="tabcontrol">
        <ul class="tabs">

          <xsl:for-each select="/data/settings/*">
            <li>
              <xsl:variable name="platform">
                <xsl:choose>
                  <xsl:when test="@name">
                    <xsl:value-of select="@name"/>
                  </xsl:when>
                  <xsl:otherwise>
                    <xsl:value-of select="name()"/>
                  </xsl:otherwise>
                </xsl:choose>
              </xsl:variable>

              <a href="#os_{position()}">
                <xsl:value-of select="$platform"/>
              </a>
            </li>
          </xsl:for-each>

        </ul>
        <div class="frames">
          <xsl:for-each select="/data/settings/*">
            <xsl:variable name="platform">
              <xsl:choose>
                <xsl:when test="@name">
                  <xsl:value-of select="@name"/>
                </xsl:when>
                <xsl:otherwise>
                  <xsl:value-of select="name()"/>
                </xsl:otherwise>
              </xsl:choose>
            </xsl:variable>
            <div class="frame" id="os_{position()}">
              <xsl:for-each select="./*">
                <xsl:variable name="browser">
                  <xsl:choose>
                    <xsl:when test="@name">
                      <xsl:value-of select="@name"/>
                    </xsl:when>
                    <xsl:otherwise>
                      <xsl:value-of select="name()"/>
                    </xsl:otherwise>
                  </xsl:choose>
                </xsl:variable>
                <div class="panel" data-role="panel">
                  <div class="heading">
                    <span class="title">
                      <xsl:value-of select="$browser"/>
                    </span>
                  </div>
                  <div class="content padding10">
                    <xsl:for-each select="./*">
                      <xsl:text> </xsl:text>
                      <label class="input-control checkbox small-check">
                        <input type="checkbox" name="version[{$platform}][{$browser}][]" value="{.}"/>
                        <span class="check"></span>
                        <span class="caption">
                          <xsl:value-of select="."/>
                        </span>
                      </label>
                      <xsl:text> </xsl:text>
                    </xsl:for-each>

                  </div>
                </div>
              </xsl:for-each>
            </div>

          </xsl:for-each>
        </div>
      </div>

      <input type="submit" name="save" value="Save" class="button primary"/>
      <input type="hidden" name="action" value="save_saucelabs_browser"/>

    </form>

  </xsl:template>


  <xsl:template name="dialog">
    <div data-role="dialog" id="dialog" class="padding20">
      <h4 class="dialogTitle"></h4>
      <p class="dialogContent"></p>
      <button class="button primary" id="dialogButton">Ok</button>
      <xsl:text> </xsl:text>
      <a href="" class="button" id="dialogButtonClose">Cancel</a>
    </div>
  </xsl:template>
</xsl:stylesheet>