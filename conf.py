# -*- coding: utf-8 -*-
import sys, os
from sphinx.highlighting import lexers
from pygments.lexers.web import PhpLexer

extensions = ['sphinx.ext.autodoc', 'sphinx.ext.doctest', 'sphinx.ext.todo', 'sphinx.ext.coverage', 'sphinx.ext.pngmath', 'sphinx.ext.mathjax', 'sphinx.ext.ifconfig']
source_suffix = '.rst'
master_doc = 'index'
project = 'Teampass API'
copyright = u'2015, ALPEIN Software SWISS Team'
version = ''
release = ''
exclude_patterns = []
html_theme = 'guzzle_sphinx_theme'
html_theme_path = ["_themes"]
htmlhelp_basename = 'Teampass API'
man_pages = [
    ('index', 'teampass', u'Teampass API',
     [u'ALPEIN Software SWISS Team'], 1)
]
