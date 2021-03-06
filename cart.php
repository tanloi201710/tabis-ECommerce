<?php
    include("session.php");
    include("layout/header.php");
    $msgh= session_id();
?>
<?php
    if(isset($_GET['action'])){
        $actions = $_GET['action'];
        switch ($actions) {
            // them vao gio hang
            case 'add':
                if(isset($_POST['index'])){
                    $id = $_POST['index'];
                }
                if(isset($_POST['quantity'])){
                    $quantt = $_POST['quantity'];
                }

                $db = mysqli_connect(host, username, password, database);
                $db->set_charset("utf8");
                $ms = "select * from giohang where MSHH=".$id;
                $result = mysqli_query($db, $ms);
                if(mysqli_num_rows($result)==1){
                    $row = mysqli_fetch_array($result, 1);
                    $add = $row['soluong'] + $quantt;
                    $sql = "UPDATE giohang SET soluong =".$add." WHERE MSHH=".$id." and MSGH='$msgh'";
                    execute($sql);
                } else{
                    $sql = "INSERT INTO giohang(MSGH,MSHH,soluong) VALUES('$msgh',$id,$quantt)";
                    execute($sql);
                }
                
                break;
            // xoa khoi gio hang
            case 'delete':
                if(isset($_GET['id'])){
                    $sql = "DELETE FROM giohang WHERE MSHH=".$_GET['id']." and MSGH='$msgh'";
                    execute($sql);
                }
                break;
            case 'update':
                if(isset($_POST['maso'])){
                    $id = $_POST['maso'];
                }
                if(isset($_POST['nb'])){
                    $quantity = $_POST['nb'];
                }
                $sql = "UPDATE giohang SET soluong=".$quantity." WHERE MSHH=".$id." and MSGH='$msgh'";
                // var_dump($sql);
                execute($sql);
                break;
        }
    }
    $tsl = "select sum(soluong) as tong from giohang where MSGH='$msgh'";
    $run = executeSingleResult($tsl);
    $size = $run['tong'];
?>

<div class="small-container align">
    <table>
        <tr>
            <th>S???n ph???m (<span><?=$size?></span>)</th>
            <th>S??? L?????ng</th>
            <th>Th??nh Ti???n</th>
            <th></th>
        </tr>
        <?php
            
            $cart = "select * from giohang where MSGH='$msgh'";
            $result = executeResult($cart);
            if(sizeof($result)>0){
                $thanhtien1=0;
                foreach ($result as $value) {
                    $get_prod = "select * from hanghoa where MSHH=".$value['MSHH'];
                    $products = executeSingleResult($get_prod);
                    $sl = $value['soluong'];
                    $name = $products['TenHH'];
                    $picture = $products['Anh'];
                    $gia = $products['Gia'];
                        echo '
                            <tr>
                                <td>
                                    <div class="cart-info">
                                        <img src="admin_area/'.$picture.'">
                                        <div class="name">
                                            <p>'.$name.'</p>
                                            <small>Gi??: '.number_format($gia, 0, ',', '.') . "??".'</small><br>
                                            <a class="text-danger" href="cart.php?action=delete&id='.$value['MSHH'].'"><span class="delete">X??a</span></a>
                                        </div>
                                    </div>
                                </td>
                                <form action="cart.php?action=update" method="post">
                                <td>
                                    <input type="number" name="nb" id="nb" value="'.$sl.'" min="1">
                                    <input type="text" name="maso" id="maso" value="'.$value['MSHH'].'" hidden>
                                </td>
                                <td>'.number_format($thanhtien=$gia*$sl, 0, ',', '.') . "??".'</td>
                                
                                <td><button class="btn btn-warning" type="submit" name="ud">C???p nh???t</button></td>
                                </form>
                            </tr>
                        ';
                        $thanhtien1 = $thanhtien1 + $thanhtien;
                } 
            }else{
                echo '
                <tr>
                    <td colspan="4">
                        <center style="height: 20rem;">
                            <img src="images/cart-empty.png" alt="" style="width: 10rem;">
                            <h4 class="text-muted">Kh??ng c?? s???n ph???m n??o trong gi??? h??ng c???a b???n</h4>
                            <a href="all_products.php"><button class="btn btn-warning">Ti???p t???c mua s???m</button></a>
                        </center>
                    </td>
                </tr>
                ';
            } 
        ?>
    </table>
    <div class="total-price">
    <?php if(sizeof($result)>0) :?>
        <table>
            <tr>
                <td>T???m t??nh</td>
                <td><?=number_format($tamtinh=$thanhtien1, 0, ',', '.') . "??"?></td>
            </tr>
            <tr>
                <td>Ph?? v???n chuy???n: </td>
                <td><?=number_format($ship = 40000, 0, ',', '.') . "??"?></td>
            </tr>
            <tr>
                <td>T???ng</td>
                <td><?=number_format($tamtinh+$ship, 0, ',', '.') . "??"?></td>
            </tr>
        </table>
    </div>
    <div class="commit">
        <form action="order.php" method="post">
            <input type="text" name="tamtinh" id="tamtinh" value="<?=$tamtinh?>" hidden>
            <button class="btn btn-commit" name="payment" type="submit">Ti???n h??nh ?????t h??ng</button>
        </form>
    </div>
    <?php endif ?>
</div>
<script>
    window.onload = function(){
        $("#cart").addClass("l-active");
    }
</script>
<?php include("layout/footer.php");?>