<?php

// 1부터 9까지 서로 다른 임의의 수 3개를 생성하는 함수
function generateNumbers() {
  $numbers = range(1, 9);
  shuffle($numbers);
  return array_slice($numbers, 0, 3);
}

// 스트라이크와 볼을 계산하는 함수
function calculateScore($input, $answer) {
  $strike = 0;
  $ball = 0;
  
  for ($i = 0; $i < 3; $i++) {
    if ($input[$i] === $answer[$i]) {
      $strike++;
    } else if (in_array($input[$i], $answer)) {
      $ball++;
    }
  }
  
  return [$strike, $ball];
}

// 게임 시작
echo "숫자 야구 게임을 시작합니다!\n";

$answer = generateNumbers();
$tryCount = 0;

while (true) {
  // 사용자로부터 입력값 받아오기
  echo "숫자 3개를 입력해주세요 (숫자와 숫자 사이에는 공백을 넣어주세요, 종료하려면 1000을 입력해주세요): ";
  fscanf(STDIN, "%d", $input);
  
  // 입력값이 1000인 경우 게임 종료
  if ($input === 10000) {
    echo "게임을 종료합니다.\n";
    break;
  }
  
  $input = str_split((string) $input);
  
  // 입력값이 3자리 수가 아닌 경우 다시 입력받기
  if (count($input) !== 3) {
    echo "잘못된 입력입니다. 숫자 3개를 입력해주세요.\n";
    continue;
  }
  
  // 스트라이크와 볼 계산하기
  $score = calculateScore($input, $answer);
  $strike = $score[0];
  $ball = $score[1];
  
  // 게임 종료 여부 판단하기
  if ($strike === 3) {
    echo "축하합니다! 정답을 맞추셨습니다.\n";
    break;
  } else {
    echo "{$strike} 스트라이크, {$ball} 볼입니다.\n";
    $tryCount++;
  }
}

echo "게임 종료! {$tryCount}번 시도하셨습니다.\n";

// 게임 종료 코드 1000 반환하기
exit(10000);
?>