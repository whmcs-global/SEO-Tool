<?php
// Input: nums = [3,2,4], target = 6
// Output: [1,2]

// function twoSum($nums, $target) {
//     $map = [];
//     for ($i = 0; $i < count($nums); $i++) {
//         $diff = $target - $nums[$i];
//         if (isset($map[$diff])) {
//             return [$map[$diff], $i];
//         }
//         $map[$nums[$i]] = $i;
//     }
//     return [];
// }

// $nums = [3, 2, 4];
// $target = 6;
// print_r(twoSum($nums, $target));


// Input: nums = [0,0,1,1,1,2,2,3,3,4]
// Output: 5, nums = [0,1,2,3,4,_,_,_,_,_]

// function removeDuplicates(&$nums) {
//     $i = 0;
//     for ($j = 1; $j < count($nums); $j++) {
//         if ($nums[$i] != $nums[$j]) {
//             $i++;
//             $nums[$i] = $nums[$j];
//         }
//     }
//     return $i + 1;
// }


$a=121;

function isPalindrome($x) {
    if ($x < 0) {
        return false;
    }
    $str = (string)$x;
    $i = 0;
    $j = strlen($str) - 1;
    while ($i < $j) {
        if ($str[$i] != $str[$j]) {
            return false;
        }
        $i++;
        $j--;
    }
    return true;
}

print_r(isPalindrome($a));

