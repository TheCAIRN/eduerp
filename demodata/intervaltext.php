<?php
$a = new DateTime('2019-10-01 12:00:00');
$b = new DateTime('2019-12-25 08:55:41');
$i = $a->diff($b);
$c = new DateTime('1970-01-01 00:00:00.000');
$c = $c->add($i);
//$c = new DateTime($i->format('00%Y-%M-%D %H:%I:%S'));
/*
echo $c->format('Y-m-d H:i:s');
echo PHP_EOL;
echo $c->getTimeStamp();
echo PHP_EOL;
$delta = rand()/getrandmax();
$delta = 1.08 - (0.16 * $delta);
echo $delta;
$delta = 1;
$n = floor($delta * $c->getTimeStamp());
echo PHP_EOL;
echo $a->add((new DateTime('1970-01-01 00:00:00.000'))->diff((new DateTime())->setTimeStamp($n)))->format('Y-m-d H:i:s');
*/
echo $a->getTimeStamp()."\r\n".$b->getTimeStamp()."\r\n".($b->getTimeStamp() - $a->getTimeStamp())."\r\n";
$delta = rand()/getrandmax();
$delta = 1.08 - (0.16 * $delta);
$n = $a->getTimeStamp() + floor($delta * ($b->getTimeStamp() - $a->getTimeStamp()));
$b->setTimeStamp($n);
echo $b->format('Y-m-d H:i:s');
?>