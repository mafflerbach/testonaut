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
          <div class="row auto-size ">
            <div class="cell size-p100 padding20" id="editform">
            </div>
          </div>
        </div>
      </body>
    </html>
  </xsl:template>

  <xsl:template name="content">

    <form class="compForm" name="compForm"
          method="post"
          action="">
      <div id="toolbar"
           style="display: none;">
        <a data-wysihtml5-command="bold"
           title="CTRL+B">
          <span class="mif-bold"></span>
        </a>
        |
        <a data-wysihtml5-command="italic"
           title="CTRL+I">
          <span class="mif-italic"></span>
        </a>
        |
        <a data-wysihtml5-command="underline"
           title="CTRL+U">
          <span class="mif-underline"></span>
        </a>
        |
        <a data-wysihtml5-command="createLink">
          <span class="mif-link"></span>
        </a>
        |
        <a data-wysihtml5-command="insertImage">
          <span class="mif-image"></span>
        </a>
        |
        <a data-wysihtml5-command="uploadFile">
          <span class="mif-upload"></span>
        </a>
        |
        <a data-wysihtml5-command="formatBlock"
           data-wysihtml5-command-value="h1">
          <span class="fa fa-header">H1</span>
        </a>
        |
        <a data-wysihtml5-command="formatBlock"
           data-wysihtml5-command-value="h2">
          <span class="fa fa-header">H2</span>
        </a>
        |
        <a data-wysihtml5-command="formatBlock"
           data-wysihtml5-command-value="h3">
          <span class="fa fa-header">H3</span>
        </a>
        |
        <a data-wysihtml5-command="formatBlock"
           data-wysihtml5-command-value="h4">
          <span class="fa fa-header">H4</span>
        </a>
        |
        <a data-wysihtml5-command="insertUnorderedList">
          <span class="mif-list"></span>
        </a>
        |
        <a data-wysihtml5-command="insertOrderedList">
          <span class="mif-list-numbered"></span>
        </a>
        |
        <a data-wysihtml5-command="foreColor"
           data-wysihtml5-command-value="red">red
        </a>
        |
        <a data-wysihtml5-command="foreColor"
           data-wysihtml5-command-value="green">green
        </a>
        |
        <a data-wysihtml5-command="foreColor"
           data-wysihtml5-command-value="blue">blue
        </a>
        |
        <a data-wysihtml5-action="change_view">
          <span class="mif-file-code"></span>
        </a>


        <div data-wysihtml5-dialog="createLink"
             class="editorBox"
             style="display: none;">
          <div class="input-control text">
            <input data-wysihtml5-dialog-field="href"
                   value="http://"
                   type="text"/>
          </div>
          <xsl:text> </xsl:text>
          <a data-wysihtml5-dialog-action="save"
             class="button primary">OK
          </a>
          <xsl:text> </xsl:text>
          <a data-wysihtml5-dialog-action="cancel"
             class="button">Cancel
          </a>
          <xsl:text> </xsl:text>
          <a data-wysihtml5-dialog-action="search"
             class="button primary">Search
            <button class="cycle-button mini-button"
                    style="position: absolute; top: -14px; right: -10px; color: darkred; display:none;">
              <span class="mif-cross"></span>
            </button>
          </a>
        </div>

        <div data-wysihtml5-dialog="insertImage"
             class="editorBox"
             style="display: none;">

          <div class="input-control text">
            <input data-wysihtml5-dialog-field="src"
                   value="http://"
                   type="text"/>
          </div>

          <label>
            <div class="input-control select" data-wysihtml5-dialog-field="className">
              <select>
                <option value="">default</option>
                <option value="wysiwyg-float-left">left</option>
                <option value="wysiwyg-float-right">right</option>
              </select>
            </div>

          </label>
          <xsl:text> </xsl:text>
          <a data-wysihtml5-dialog-action="save"
             class="button primary">OK
          </a>
          <xsl:text> </xsl:text>
          <a data-wysihtml5-dialog-action="cancel"
             class="button">Cancel
          </a>
          <xsl:text> </xsl:text>
          <a data-wysihtml5-dialog-action="searchImage"
             class="button primary">Search
            <button class="cycle-button mini-button"
                    style="position: absolute; top: -14px; right: -10px; color: darkred; display:none;">
              <span class="mif-cross"></span>
            </button>
          </a>
        </div>

        <div data-wysihtml5-dialog="search"
             class="editorBox"
             style="display: none;">

          <div class="input-control text">
            <input type="text"
                   name="search"
                   value=""
                   placeholder="search for file"/>
          </div>

          <div style="width: 640px" id="listFile"></div>
        </div>
      </div>

      <div data-wysihtml5-dialog="searchImage"
           class="editorBox"
           style="display: none;">
        <div class="input-control text">
          <input type="text"
                 name="searchImage"
                 value=""
                 placeholder="search for file"/>
        </div>

        <div class="tile-container bg-darkCobalt" style="width: 640px" id="listimage">
        </div>
      </div>

      <br/>

      <textarea id="textarea" name="pageContent"
                placeholder="Enter text ...">
        <xsl:value-of select="/data/page/content"/>
      </textarea>
      <br/>
      <br/>
      <input type="hidden"
             name="path"
             value="{/data/system/baseUrl}/{requestUri}"/>
      <input type="submit"
             name="action"
             value="save"
             class="button primary"/>
      <xsl:text> </xsl:text>
      <a href="{/data/system/baseUrl}{/data/page/path}"
         class="backLink button">Zurück
      </a>
      <input type="hidden"
             name="content"
             value=""/>
    </form>


    <script>
      initEditor()
    </script>
  </xsl:template>
</xsl:stylesheet>