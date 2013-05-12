<?php
print_r(\preg_split('/(\[|\])+/', 'asd', 0, PREG_SPLIT_NO_EMPTY));
print_r(\preg_split('/(\[|\])+/', 'asd[x]', 0, PREG_SPLIT_NO_EMPTY));
print_r(\preg_split('/(\[|\])+/', 'asd[x][y]', 0, PREG_SPLIT_NO_EMPTY));