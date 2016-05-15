<?php

  /**
   * Created by PhpStorm.
   * User: maren
   * Date: 03.05.14
   * Time: 11:47
   */

namespace testonaut\Utils;
  class Diff {
    private $patch;

    public function __construct($patch = "") {
      $this->patch = $patch;
    }

    public function setPatch($patch) {
      $this->patch = $patch;
    }

    private function getCommits() {
      $test = explode('diff --git', $this->patch);
      $commit = array();

      for ($i = 1; $i < count($test); $i++) {
        $commit[] = array(
          'filename' => 'file' . $i,
          'patch'    => $test[$i]
        );
      }

      return $commit;
    }

    private function replaceGitMarker($line) {
      $line = str_replace('[- eirmod', '<mark class="bg-red fg-white">', $line);
      $line = str_replace('{+ eirmod', '<mark class="bg-emerald fg-white">', $line);
      $line = str_replace('eirmod[-', '<mark class="bg-red fg-white">', $line);
      $line = str_replace('eirmod{+', '<mark class="bg-emerald fg-white">', $line);
      $line = str_replace('[-eirmod', '<mark class="bg-red fg-white">', $line);
      $line = str_replace('{+eirmod', '<mark class="bg-emerald fg-white">', $line);
      $line = str_replace('[-', '<mark class="bg-red fg-white">', $line);
      $line = str_replace('{+', '<mark class="bg-emerald fg-white">', $line);
      $line = str_replace('+}', '</mark>', $line);
      $line = str_replace('-]', '</mark>', $line);

      return $line;
    }

    private function removeTags($line) {
      $line = str_replace('  ', '', $line);
      $line = str_replace('<tr>', '', $line);
      $line = str_replace('</tr>', '', $line);
      $line = str_replace('<td rowspan="1" colspan="3">', '', $line);
      $line = str_replace('<td>', '', $line);
      $line = str_replace('</td>', '', $line);
      $line = str_replace('<thead>', '', $line);
      $line = str_replace('</thead>', '', $line);
      $line = str_replace('<thead>', '', $line);
      $line = str_replace('</thead>', '', $line);
      $line = str_replace('<tbody>', '', $line);
      $line = str_replace('</tbody>', '', $line);
      $line = str_replace('<table>', '', $line);
      $line = str_replace('</table>', '', $line);

      return $line;
    }

    private function removeSpecialHtmlTags($line){

      $line = str_replace('<br>', "\n", $line);
      $line = str_replace('<p>', '', $line);
      $line = str_replace('</p>', "\n", $line);
      $line = str_replace('<div>', '', $line);
      $line = str_replace('</div>', "\n", $line);
      return $line;

    }


    private function printLine($line) {

      $line = htmlspecialchars($line);
      $line = $this->replaceGitMarker($line);

      return ('<li>
                  <pre class="code">' . str_replace('  ', '', $line) . '</pre>           
              </li>');
    }

    private function buildTable($file, $removeTags) {
      $table = '';
      $firstLine = TRUE;
      $lines = explode("\n", $file['patch']);

      $table .= '<ul class="list-unstyled">';
      $i = 0;
      foreach ($lines as $line) {
        if ($removeTags) {
          $line = $this->removeTags($line);
        }
        $line = $this->removeSpecialHtmlTags($line);

        if ($line == '') {
          continue;
        }
        if ($i <= 4) {
          $i++;
          continue;
        }

        if(substr($line, 0, 2) == '@@') {
          continue;
        }

        if ($line == '') {
          continue;
        }

        if (!$firstLine) {
          $table .= $this->printLine($line);
        } else {
          $table .= $this->printLine($line);
          $firstLine = FALSE;
        }
      }
      $table .= '</ul>';

      return $table;
    }

    private function parsePath($patch) {
      $lines = explode("\n", $patch['patch']);
      $firstLine = explode(' ', $lines[0]);
      $path = str_replace('/content', '', substr($firstLine[1], 2, strlen($firstLine[1])));
      $path = str_replace('content', '', $path);
      $path = str_replace('config', '', $path);
      $path = str_replace('/config', '', $path);
      $path = str_replace('/', '.', $path);

      return $path;
    }

    public function buildDiff($removeTags) {
      $commit = $this->getCommits();
      $output = '';

      foreach ($commit as $file) {
        $output .= '<div style="overflow: auto">';
        $output .= $this->buildTable($file, $removeTags);
        $output .= '</div>';
        $content[] = array(
          'content' => $output,
          'path' => $this->parsePath($file)
        );
        $output = '';
      }

      return $content ;
    }
  }