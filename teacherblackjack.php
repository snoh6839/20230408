<?php

define("STR_CARD_NUM", "card_num");
define("STR_CARD_SHAPE_KEY", "card_shape_key");

class Card
{
    private $arr_card_num;         // 카드 번호
    private $arr_card_shape;    // 카드 모양
    public function __construct()
    {
        $this->arr_card_num        = array("A", "2", "3", "4", "5", "6", "7", "8", "9", "10", "J", "Q", "K");
        $this->arr_card_shape    = array("♥", "◆", "♣", "♠");
    }

    public function get_card_num_score($param_num, $param_score)
    {
        switch ($param_num) {
            case "J":
            case "Q":
            case "K":
                return 10;
            case "A":
                if ($param_score > 10) {
                    return 1;
                } else {
                    return 11;
                }
            default:
                return (int)$param_num;
        }
    }

    public function get_card_shape($param_key)
    {
        return $this->arr_card_shape[$param_key];
    }

    public function get_arr_card_num()
    {
        return $this->arr_card_num;
    }

    public function get_arr_card_shape()
    {
        return $this->arr_card_shape;
    }

    public function get_cnt_card()
    {
        return $this->cnt_card;
    }
}

class Deck
{
    private $arr_deck;        // 게임용 덱
    private $cnt_deck;        // 덱 카운트
    private $size_deck;        // 덱 사이즈

    public function __construct()
    {
        $obj_card = new Card();
        $this->set_deck($obj_card->get_arr_card_num(), $obj_card->get_arr_card_shape());
        $this->cnt_deck    = 0;
        $this->size_deck = sizeof($this->arr_deck);
        $obj_card = null;
    }

    private function set_deck(array $param_arr_card_num, array $param_arr_card_shape): void
    {
        // 카드 52장 덱에 셋팅 ( 예 : array( array( "card_num" => 1, "card_shape" => 1 ), ... ) )
        foreach ($param_arr_card_shape as $shape_key => $shape_val) {
            foreach ($param_arr_card_num as $num) {
                $this->arr_deck[] = array(STR_CARD_NUM => $num, STR_CARD_SHAPE_KEY => $shape_key);
            }
        }
        // 덱 셔플
        shuffle($this->arr_deck);

    }

    public function give_card(): array
    {
        if ($this->cnt_deck === $this->size_deck) {
            throw new Exception('Deck is empty');
        }
        $card = array_shift($this->arr_deck);

        // 덱 카운트 1증가
        $this->cnt_deck++;

        return $card;
    }
}

class Player
{
    private $hand;
    private $score;
    private $obj_card;
    protected $player_name;

    public function __construct()
    {
        $this->hand = array();
        $this->score = 0;
        $this->obj_card = new Card();
    }

    public function print_open_card()
    {
        $str = $this->player_name;
        foreach ($this->hand as $card) {
            $str .= $this->obj_card->get_card_shape($card[STR_CARD_SHAPE_KEY]) . $card[STR_CARD_NUM] . " ";
        }
        echo $str;
    }

    public function clear_var()
    {
        $this->hand = array();
        $this->score = 0;
    }

    public function set_score($param_score)
    {
        $this->score += $param_score;
    }

    public function get_score()
    {
        return $this->score;
    }

    public function set_var(&$param_obj_deck)
    {
        $arr_card = $param_obj_deck->give_card();
        $this->hand[] = $arr_card;
        $this->set_score($this->obj_card->get_card_num_score($arr_card[STR_CARD_NUM], $this->get_score()));

    }

    public function get_hand()
    {
        return $this->hand;
    }
}

class User extends Player
{
    public function __construct()
    {
        parent::__construct();
        $this->player_name = "USER : "; // USER로 설정
    }
}

class Dealer extends Player
{
    public function __construct()
    {
        parent::__construct();
        $this->player_name = "DEALER : "; // DEALER로 설정
    }

    public function set_var(&$param_obj_deck)
    {
        if ($this->get_score() < 17) {
            parent::set_var($param_obj_deck);
        }
    }
}

class Play
{
    private $obj_user;        // 유저 클래스
    private $obj_dealer;    // 딜러 클래스
    private $obj_deck;        // 덱 클래스

    public function __construct(User $user, Dealer $dealer, Deck $deck)
    {
        $this->obj_user        = new User();
        $this->obj_dealer    = new Dealer();
        $this->obj_deck        = new Deck();
    }

    public function set_card()
    {
        if (sizeof($this->obj_user->get_hand()) > 0) {
            $this->obj_user->set_var($this->obj_deck);
            $this->obj_dealer->set_var($this->obj_deck);
        } else {
            $this->obj_user->set_var($this->obj_deck);
            $this->obj_dealer->set_var($this->obj_deck);
            $this->obj_user->set_var($this->obj_deck);
            $this->obj_dealer->set_var($this->obj_deck);
        }
    }

    public function chk_score()
    {
        $user_score = $this->obj_user->get_score();
        $dealer_score = $this->obj_dealer->get_score();
        $flg_burst = false;
        $arr_player = array();
        $str_con = "";

        if (($user_score > 21 && $dealer_score > 21)) {
            $arr_player[] = "User";
            $arr_player[] = "Dealer";
            $int_result = 0;
            $str_con = "Burst";
            $flg_burst = true;
        } else if ($user_score > 21) {
            $arr_player[] = "User";
            $int_result = 2;
            $str_con = "Burst";
            $flg_burst = true;
        } else if ($dealer_score > 21) {
            $arr_player[] = "Dealer";
            $int_result = 1;
            $str_con = "Burst";
            $flg_burst = true;
        } else if ($user_score === 21) {
            $arr_player[] = "User";
            $int_result = 1;
            $str_con = "Black Jack!!";
            $flg_burst = true;
        } else if ($dealer_score === 21) {
            $arr_player[] = "Dealer";
            $int_result = 2;
            $str_con = "Black Jack!!";
            $flg_burst = true;
        }

        if ($flg_burst) {
            $this->print_result($arr_player, $int_result, $str_con);
        }

        return $flg_burst;
    }

    public function chk_result()
    {
        $user_score = $this->obj_user->get_score();
        $dealer_score = $this->obj_dealer->get_score();
        $flg_burst = false;
        $arr_player = array();

        if ($user_score > $dealer_score) {
            $arr_player[] = "User";
            $int_result = 1;
            $flg_burst = true;
        } else if ($user_score < $dealer_score) {
            $arr_player[] = "Dealer";
            $int_result = 2;
            $flg_burst = true;
        } else if ($user_score === $dealer_score) {
            $int_result = 1;
            $flg_burst = true;
        }

        $this->print_result($arr_player, $int_result);
    }

    public function print_result($param_arr, $param_int_result, $param_str_con = "")
    {
        $str = implode(", ", $param_arr) . " ";
        switch ($param_int_result) {
            case 0:
                $str .= $param_str_con . ", 무승부입니다.\n";
                break;
            case 1:
                $str .= $param_str_con . ", 승리했습니다.\n";
                break;
            case 2:
                $str .= $param_str_con . ", 패배했습니다.\n";
                break;
        }

        echo $str;
        $this->obj_user->print_open_card();
        echo " / ";
        $this->obj_dealer->print_open_card();
        echo "\n";
    }

    public function open_player_card()
    {
        $this->obj_user->print_open_card();

        echo "\n";
    }

    public function clare_player_card()
    {
        $this->obj_user->clear_var();
        $this->obj_dealer->clear_var();
    }

    public function game_start()
    {
        $flg_clare_card = true;
        echo "----- Black Jack -----\n";
        while (true) {
            if ($flg_clare_card) {
                echo "----- New Game -----\n";
                $this->set_card();
                $flg_clare_card = false;
                if ($this->chk_score()) {
                    $this->clare_player_card();
                    $flg_clare_card = true;
                } else {
                    $this->open_player_card();
                }
            }
            fscanf(STDIN, "%d", $input);
            echo "입력값 : $input \n";

            if ($input === 1) {
                $this->set_card();
                $this->open_player_card();
                if ($this->chk_score()) {
                    $this->clare_player_card();
                    $flg_clare_card = true;
                }
            } else if ($input === 2) {
                if (!$this->chk_score()) {
                    $this->chk_result();
                }
                $this->clare_player_card();
                $flg_clare_card = true;
            } else if ($input === 0) {
                break;
            } else {
                echo "잘못 입력하셨습니다.\n";
                $input = 1;
            }
            print "\n";
        }
        echo "끝!\n";
    }
}


$obj_card = new Card();
$obj_user = new User($obj_card);
$obj_dealer = new Dealer($obj_card);
$obj_deck = new Deck();
$obj_play = new Play($obj_user, $obj_dealer, $obj_deck);
$obj_play->game_start();