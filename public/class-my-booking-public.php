<?php

/**
 * Created by PhpStorm.
 * User: frank
 * Date: 04/11/2016
 * Time: 11:49
 */
class MY_Booking_Public
{
    private $plugin_name;
    private $version;

    public function __construct($plugin_name, $version)
    {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

    }

    function enqueue_scripts()
    {
        //wp_enqueue_style('jquery.realperson',  plugins_url('/css/jquery.realperson.css',__FILE__) );
        //wp_enqueue_style('datetimepicker', plugins_url('/css/datetimepicker.css',__FILE__) );
        wp_enqueue_style('select2', plugins_url('/css/select2.min.css', __FILE__));
        wp_enqueue_script('select2', plugins_url('/js/select2.full.min.js', __FILE__), array('jquery'), 4.0, true);
        //wp_enqueue_script( 'jquery', plugins_url( '/js/jquery.js',__FILE__) , array ( 'jquery' ), 1.1, true);
        //wp_enqueue_script('jquery.realperson',  plugins_url( '/js/jquery.realperson.js',__FILE__) , array(), false, true);
        //wp_enqueue_script('jquery.plugin', plugins_url( '/js/jquery.plugin.js',__FILE__), array(), false, true);
        //wp_enqueue_script('moment', plugins_url( '/js/moment.js',__FILE__)  , array(), false, true);
        //wp_enqueue_script('bootstrap-datetimepicker', plugins_url( '/js/bootstrap-datetimepicker.js',__FILE__) , array(), false, true);
    }

    public function wp_ajax_book()
    {
        $this->data_book("cuba");
    }

    public function wp_ajax_book_cart()
    {
        $this->data_book("autos");
    }

    public function wp_ajax_book_hotel()
    {
        $this->data_book("reserva-hotel");
    }

    public function wp_ajax_book_internacional()
    {
        $this->data_book("internacionales");
    }

    public function data_book($term)
    {
        if (isset($_POST['form'])) {
            $contacts = $_POST['form'];

            foreach ($contacts as $contact) {

                if ($contact["name"] == "destino") {

                    $destino = $contact["value"];
                }
                if ($contact["name"] == "lastname") {

                    $apellido = $contact["value"];
                }

                if ($contact["name"] == "startdate") {

                    $fechainicial = $contact["value"];
                }
                if ($contact["name"] == "finaldate") {

                    $fechafinal = $contact["value"];
                }
                if ($contact["name"] == "captcha") {

                    $captchavalue = $contact["value"];
                }
                if ($contact["name"] == "captchaHash") {

                    $captchahash = $contact["value"];
                }

            }

            if (($this->valida_date($fechainicial, $fechafinal)) == false) {
                echo "3";
            } elseif ($this->rpHash($captchavalue) == $captchahash) {

                $args = array('post_title' => $apellido . '-reserva-' . $destino,
                    'post_type' => 'reservaciones',
                    'post_status' => 'publish');
                $post_id = wp_insert_post($args);


                if (!is_wp_error($post_id)) {
                    wp_set_object_terms($post_id, $term, "tipos");

                    foreach ($contacts as $contact) {
                        if (($contact["name"] != "captcha") && ($contact["name"] != "captchaHash")) {
                            update_post_meta($post_id, $contact["name"], $contact["value"]);
                        }

                    }

                }

                echo "1";

            } else {
                echo "2";
            }


        }


    }
    public function wp_ajax_contact(){
        if (isset($_POST['form'])) {
            echo 1;
        }
        wp_die();

    }


    public function valida_date($date1, $date2)
    {
        $date1 = substr($date1, 0, -8);
        $date2 = substr($date2, 0, -8);
        $date1 = explode("/", $date1);
        $date2 = explode("/", $date2);

        if ($date2[2] < $date1[2]) {

            return false;
        } elseif ($date2[0] < $date1[0]) {
            return false;


        } elseif ($date2[1] < $date1[1]) {

            return false;
        } else {
            return true;
        }

    }

    function rpHash($value)
    {
        $hash = 5381;
        $value = strtoupper($value);
        for ($i = 0; $i < strlen($value); $i++) {
            $hash = (($hash << 5) + $hash) + ord(substr($value, $i));
        }
        return $hash;
    }


    public function my_booking_shortcode()
    {


        $form = '<section id="' . get_the_title() . '">
                <div class="col-xs-12">                           
                                    <section id="formulario">
                                        <div class="row">

                                            <div class="col-xs-12">
                                                <form id="bookingform" class="form-horizontal" role="form" action="' . home_url('/') . '" 
                                                      method="post">
                                                    <div class="col-xs-12">
                                                     <div class="col-xs-12 text-center">
                                                            <h3>Datos del Viaje</h3> 
                                                              </div>
                                                       <div class="form-group">
                                                            <label for="destino" class="col-lg-2 control-label">Destino</label>

                                                            <div class="col-lg-10">
                                                            ' . $this->my_destino_select() . '
                                                                
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                             <label for="fecha_inicial" class="col-lg-2 control-label">Fecha Inicial</label>
                                                             <div class="col-lg-10">
                                                             <input type="text" class="form-control" required name="startdate" id="startdate"
                                                                       value=""
                                                                       placeholder="Fecha Inicial">
                                                             </div>
                                                             </div>
                                                             <div class="form-group">
                                                             <label for="fecha_final" class="col-lg-2 control-label">Fecha Final</label>
                                                             <div class="col-lg-10">
                                                             <input type="text" class="form-control" required name="finaldate" id="finaldate"
                                                              value="" placeholder="Fecha Final">
                                                             </div>
                                                             </div>';
        $form .= $this->datos_personales();
        $form .= '                                                          
                                                            <div class="col-xs-12 text-center">
                                                            <h3>Datos Pasaporte</h3> 
                                                              </div>
                                                            <div class="form-group">
                                                            <label for="pasaporte_cu" class="col-lg-2 control-label">Pasaporte Cubano</label>

                                                            <div class="col-lg-10">
                                                                <input  id ="pasaporte_cu" type="text" class="form-control" required name="pasaporte_cu"
                                                                       value="" placeholder="Pasaporte Cubano">
                                                            </div>
                                                            </div>
                                                            <div class="form-group">
                                                             <label for="fecha_vencimiento" class="col-lg-2 control-label">Fecha de Vencimiento</label>
                                                             <div class="col-lg-10">
                                                             <input type="text" class="form-control" required name="fecha_vec" id="fecha_vec"
                                                              value="" placeholder="Fecha de Vencimiento">
                                                             </div>
                                                             </div>
                                                             <div class="form-group">
                                                            <label for="pasaporte" class="col-lg-2 control-label">Pasaporte Extranjero o Residencia</label>

                                                            <div class="col-lg-10">
                                                                <input  id ="pasaporte" type="text" class="form-control" required name="pasaporte"
                                                                       value="" placeholder="Pasaporte Extranjero o Residencia">
                                                            </div>
                                                            </div>
                                                            <div class="form-group">
                                                             <label for="fecha_vencimiento_ex" class="col-lg-2 control-label">Fecha de Vencimiento</label>
                                                             <div class="col-lg-10">
                                                             <input type="text" class="form-control" required name="fecha_vec_ex" id="fecha_vec_ex"
                                                              value="" placeholder="Fecha de Vencimiento">
                                                             </div>
                                                             </div>
                                                             <div class="form-group">
                                                            <label for="direccion_cu" class="col-lg-2 control-label">Dirección en Cuba</label>

                                                            <div class="col-lg-10">
                                                                <input  id ="direccion_cu" type="text" class="form-control" required name="direccion_cu"
                                                                       value="" placeholder="Dirección en Cuba">
                                                            </div>
                                                            </div>
                                                            <div class="form-group">
                                                            <label for="provincia" class="col-lg-2 control-label">Provincia</label>
                                                            <div class="col-lg-10">
                                                                ' . $this->provincia_select() . '
                                                            </div>
                                                            </div>';
        $form .= $this->mycapcha_field();
        /*<div class="col-xs-12 text-center">
         <h3>Eres un Robot?</h3>
           </div>

         <div id="captchaform" class="form-group">
         <label id="label_capcha" for="capcha" class="col-lg-2 control-label">Teclea el código</label>
         <script>
             jQuery(function() {
                 jQuery(\'#captcha\').realperson();
             });
         </script>

         <div class="col-lg-10">
             <input id="captcha" type="text" class="form-control" required name="captcha" id="captcha"
                    value=""
                    placeholder="Teclea el código">';*/


        $form .= '       
                                                            <div class="modal-footer">
                                   
                                    <button type="submit"    id="buttonsubmit" class="btn btn-primary">Reserve ahora</button>
                                </div>
                                                            </form>
                                                            </div></div></div></div></section></div></section>';

        $form .= $this->myscript_datetimepicker("startdate", "finaldate");
        $form .= $this->simple_datetimepicker("fecha_vec");
        $form .= $this->simple_datetimepicker("fecha_vec_ex");
        $form .= $this->nac_datetimepicker("fecha_nac");

        /* $form .= '<script type="text/javascript">


    /* jQuery(document).ready(function () {


         jQuery(\'#startdate\').datetimepicker({
             minDate: \'now\'
         }).on(\'dp.change dp.update\', function (e) {
             //jQuery(\'#finaldate\').datetimepicker({ minDate: e.date })
            //console.log(e.date)
            if(jQuery(this).on("dp.update")){

             jQuery(\'#finaldate\').datetimepicker({minDate: e.date,defaultDate:e.date})

             console.log(e.date._d);}

         }) });*/
        /*
          jQuery(document).ready(function(){

             var fecha = new Date("now");
          jQuery(\'#fecha_nac\').datetimepicker({maxDate:"now",
                                                  minDate:"1945-01-01"});

          //jQuery(\'#fecha_vec\').datetimepicker({minDate:"now"});
           //jQuery(\'#fecha_vec_ex\').datetimepicker({minDate:"now"});       })





  </script>';*/

        echo $form;
    }

    public function my_destino_select()
    {
        $args = array("post_type" => "provincias",
            "post_status" => "publish",
            "posts_per_page" => -1,
            "order" => "ASC");
        $provincias = get_posts($args);
        $select = '<select class="form-control" name="destino" id="destino">';
        foreach ($provincias as $post):setup_postdata($post);
            $select .= '<option value=' . $post->post_title . '>' . $post->post_title . '</option>';
        endforeach;
        $select .= '</select>';
        wp_reset_postdata();
        return $select;


    }

    public function datos_personales()
    {
        $form = '
        <div class="col-xs-12 text-center">
                                                            <h3>Datos Personales</h3> 
                                                              </div>
                                                        <div class="form-group">
                                                            <label for="contactName" class="col-lg-2 control-label">Name</label>

                                                            <div class="col-lg-10">
                                                                <input  id ="contact_name"type="text" class="form-control" required name="contactName"
                                                                       value="" placeholder="Name">
                                                            </div>
                                                            </div>
                                                            <div class="form-group">
                                                            <label for="lastname" class="col-lg-2 control-label">Last Name</label>

                                                            <div class="col-lg-10">
                                                                <input  id ="lastname" type="text" class="form-control" required name="lastname"
                                                                       value="" placeholder="Last Name">
                                                            </div>
                                                        </div>
                                                        
                                                       <div class="form-group">
                                                             <label for="email" class="col-lg-2 control-label">Email</label>
                                                             <div class="col-lg-10">
                                                             <input type="email" class="form-control" required name="email" id="email"
                                                                       value=""
                                                                       placeholder="Email">
                                                             </div>

                                                             </div>
                                                             
                                                            
                                                            <div class="form-group">
                                                            <label for="sexo" class="col-lg-2 control-label">Sexo</label>
                                                            <div class="col-lg-10">
                                                                <select class="form-control" name="sexo" id="sexo">
                                                                    <option value="Masculino">Masculino</option>
                                                                    <option value="Femenino">Femenino</option>
                                                                </select>
                                                            </div>
                                                            </div>
                                                            <div class="form-group">
                                                             <label for="fecha_nacimiento" class="col-lg-2 control-label">Fecha de nacimiento</label>
                                                             <div class="col-lg-10">
                                                             <input type="date" class="form-control" required name="fecha_nac" id="fecha_nac"
                                                              value="" placeholder="Fecha de Nacimiento">
                                                             </div>
                                                             </div>
                                                             <div class="form-group">
                                                              <label for="direccion" class="col-lg-2 control-label">Dirección</label>

                                                            <div class="col-lg-10">
                                                                <input  id ="direccion" type="text" class="form-control" required name="direccion"
                                                                       value="" placeholder="Dirección">
                                                            </div>
                                                            </div>
                                                            <div class="form-group">
                                                            <label for="ciudad" class="col-lg-2 control-label">Ciudad</label>

                                                            <div class="col-lg-10">
                                                                <input  id ="ciudad" type="text" class="form-control" required name="ciudad"
                                                                       value="" placeholder="Ciudad">
                                                            </div>
                                                            </div>
                                                            <div class="form-group">
                                                            <label for="estado" class="col-lg-2 control-label">Estado</label>
                                                            <div class="col-lg-10">
                                                           ' . $this->estado_select() . '
                                                            </div>
                                                            </div>
                                                            
                                                            <div class="form-group">
                                                            <label for="zip_code" class="col-lg-2 control-label">Código Postal</label>

                                                            <div class="col-lg-10">
                                                                <input  id ="zip_code" type="num" class="form-control" required name="zip_code"
                                                                       value="" placeholder="Código Postal">
                                                            </div>
                                                            </div>
                                                            <div class="form-group">
                                                              <label for="Phone" class="col-lg-2 control-label">Telefono</label>

                                                            <div class="col-lg-10">
                                                                <input  id ="telefono" type="phone" class="form-control" required name="telefono"
                                                                       value="" placeholder="xxx-xxx-xxxx" pattern="^\d{3}-\d{3}-\d{4}$">
                                                            </div>
                                                            </div>
                                                            ';
        return $form;

    }

    public function estado_select()
    {
        $estado = '<select class="form-control" name="estado" id="estado">
	<option value="Alabama">Alabama</option>
	<option value="Alaska">Alaska</option>
	<option value="Arizona">Arizona</option>
	<option value="Arkansas">Arkansas</option>
	<option value="California">California</option>
	<option value="Colorado">Colorado</option>
	<option value="Connecticut">Connecticut</option>
	<option value="Delaware">Delaware</option>
	<option value="District of Columbia">District of Columbia</option>
	<option value="Florida">Florida</option>
	<option value="Georgia">Georgia</option>
	<option value="Hawaii">Hawaii</option>
	<option value="Idaho">Idaho</option>
	<option value="Illinois">Illinois</option>
	<option value="Indiana">Indiana</option>
	<option value="Iowa">Iowa</option>
	<option value="Kansas">Kansas</option>
	<option value="Kentucky">Kentucky</option>
	<option value="Louisiana">Louisiana</option>
	<option value="Maine">Maine</option>
	<option value="Maryland">Maryland</option>
	<option value="Massachusetts">Massachusetts</option>
	<option value="Michigan">Michigan</option>
	<option value="Minnesota">Minnesota</option>
	<option value="Mississippi">Mississippi</option>
	<option value="Missouri">Missouri</option>
	<option value="Montana">Montana</option>
	<option value="Nebraska">Nebraska</option>
	<option value="NV">Nevada</option>
	<option value="New Hampshire">New Hampshire</option>
	<option value="New Jersey">New Jersey</option>
	<option value="New Mexico">New Mexico</option>
	<option value="New York">New York</option>
	<option value="North Carolina">North Carolina</option>
	<option value="North Dakota">North Dakota</option>
	<option value="Ohio">Ohio</option>
	<option value="Oklahoma">Oklahoma</option>
	<option value="Oregon">Oregon</option>
	<option value="Pennsylvania">Pennsylvania</option>
	<option value="Rhode Island">Rhode Island</option>
	<option value="South Carolina">South Carolina</option>
	<option value="South Dakota">South Dakota</option>
	<option value="Tennessee">Tennessee</option>
	<option value="Texas">Texas</option>
	<option value="Utah">Utah</option>
	<option value="Vermont">Vermont</option>
	<option value="Virginia">Virginia</option>
	<option value="Washington">Washington</option>
	<option value="West Virginia">West Virginia</option>
	<option value="WI">Wisconsin</option>
	<option value="Wyoming">Wyoming</option>
</select>';
        return $estado;
    }

    public function provincia_select()
    {
        $provincia = '<select class="form-control" name="prov" id="prov">
	<option value="Pinar del Rio">Pinar del Rio</option>
	<option value="Artemisa">Artemisa</option>
	<option value="Cuidad Habana">Cuidad Habana</option>
	<option value="Mayabeque">Mayabeque</option>
	<option value="Matanzas">Matanzas</option>
	<option value="Villa Clara">Villa Clara</option>
	<option value="Santi Espiritud">Santi Espiritud</option>
	<option value="Ciego de Avila">Ciego de Avila</option>
	<option value="Camaguey">Camaguey</option>	
	<option value="Ciego de Avila">Ciego de Avila</option>
	<option value="Camaguey">Camaguey</option>
	<option value="Las Tunas">Las Tunas</option>
	<option value="Holguin">Holguin</option>
	<option value="Granma">Granma</option>
	<option value="Santiago de Cuba">Santiago de Cuba</option>
	<option value="Guantanamo">Guantanamo</option>
	<option value="Isla de la Juventud">Isla de la Juventud</option>
		</select>';
        return $provincia;

    }

    public function mycapcha_field()
    {
        $form = '<div class="col-xs-12 text-center">
                                                            <h3>Eres un Robot?</h3> 
                                                              </div>
                                                              
                                                            <div id="captchaform" class="form-group">
                                                            <label id="label_capcha" for="capcha" class="col-lg-2 control-label">Teclea el código</label>
                                                            <script>
                                                                jQuery(function() {
                                                                    jQuery(\'#captcha\').realperson();
                                                                });
                                                            </script>
                                                            
                                                            <div class="col-lg-10">
                                                                <input id="captcha" type="text" class="form-control" required name="captcha" id="captcha"
                                                                       value=""
                                                                       placeholder="Teclea el código">
                                                                       </div>
                                                        </div>';
        return $form;
    }

    public function myscript_datetimepicker($startdate, $finaldate)
    {
        $form = '<script type="text/javascript">


    jQuery(document).ready(function () {


        jQuery("#' . $startdate . '").datetimepicker({
            minDate: \'now\'
        }).on(\'dp.change dp.update\', function (e) {
            //jQuery(\'#finaldate\').datetimepicker({ minDate: e.date })
           //console.log(e.date)
           if(jQuery(this).on("dp.update")){
           
            jQuery("#' . $finaldate . '").datetimepicker({minDate: e.date,defaultDate:e.date})
            
            console.log(e.date._d);}
            
        }) });</script>';
        return $form;


    }

    public function simple_datetimepicker($fecha)
    {
        $form = '<script type="text/javascript">
    jQuery(document).ready(function () {
        jQuery("#' . $fecha . '").datetimepicker({minDate:"now"});
                 
                        })
                
                      </script>';
        return $form;

    }

    public function nac_datetimepicker($fecha)
    {
        $form = '<script type="text/javascript">
                 jQuery(document).ready(function () {
                     
                 jQuery("#' . $fecha . '").datetimepicker({minDate:"1945-01-01",maxDate:"now"});    
                 })</script>';
        return $form;

    }

    /* public function select2($select)
     {
         ?>
         <script>
             jQuery(document).ready(function () {
                 jQuery(".<?php $select ?>").select2();
             });
         </script>
         <?php
     }*/

    public function my_wp_head_ajax_url()
    {
        ?>
        <script>
            var ajaxurl = '<?php echo admin_url("admin-ajax.php");?>';
        </script>
        <?php
    }

    public function my_script_footer()
    {
        $msg = "la fecha final es menor que la inicial";

        $this->ajax_data('bookingform', 'captchaform', 'book', $msg);
    }

    public function my_script_footer_cart()
    {
        $msg = "la fecha de entrega es menor que la de recogida";

        $this->ajax_data('bookingformcart', 'captchaform', 'book_cart', $msg);
    }

    public function my_script_footer_hotel()
    {
        $msg = "la fecha final es menor que la inicial";

        $this->ajax_data('bookingformhotel', 'captchaform', 'book_hotel', $msg);
    }

    public function my_script_footer_internacional()
    {
        $msg = "la fecha final es menor que la inicial";

        $this->ajax_data('bookingforminternacional', 'captchaform', 'book_internacional', $msg);
    }


    public function ajax_data($form, $capcha, $action, $msg)
    {

        ?>
        <script>
            jQuery(document).ready(function () {
                jQuery('#<?php echo $form ?>').submit(function (event) {

                    event.preventDefault();
                    event.stopPropagation();
                    //jQuery('#<?php echo $form ?>').prop('disabled',true);

                    //var ajaxurl = '/wp-admin/admin-ajax.php';
                    form = jQuery('#<?php echo $form ?>').serializeArray();
                    jQuery.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        timeout: 5000,
                        dataType: 'html',
                        data: {action: '<?php echo $action ?>', form: form},
                        error: function (xml) {
                            //timeout, but no need to scare the user
                        },
                        success: function (response) {
                            console.log(response);
                            if (response == 30) {
                                jQuery('#<?php echo $capcha ?>').after(
                                    '<div  class="col-xs-12 "><span id="baddate" class="alert alert-danger"><?php echo $msg ?></span></div>');
                                //jQuery('#<?php echo $form ?>').prop('disabled',false);
                                setTimeout(function () {
                                    jQuery('#baddate').remove()
                                }, 3000);
                            }
                            else if (response == 10) {
                                jQuery('#<?php echo $capcha ?>').after(
                                    '<div  class="col-xs-12 "><span id="enviado" class="alert alert-success">Usted ha Reservado</span></div>');
                                setTimeout(function () {
                                    jQuery('#enviado').remove()
                                }, 3000);
                                //jQuery('#bookingform').submit();
                                //jQuery('#<?php echo $form ?>').prop('disabled',false);
                                jQuery("#<?php echo $form ?>")[0].reset();
                                //jQuery('#buttonsubmit').reload();

                            }
                            else {
                                jQuery('#<?php echo $capcha ?>').after(
                                    '<div id="spansucces" class="col-xs-12"><span  class="alert alert-danger">código erroneo</span></div>');
                                //jQuery('#<?php echo $form ?>').prop('disabled',false);
                                setTimeout(function () {
                                    jQuery('#spansucces').remove()
                                }, 3000);
                            }
                        }
                    });
                })
            })
        </script> <?php
    }

    public function wp_ajax_select_country()
    {
        if (isset($_GET['value'])) {
            $value = $_GET['value'];
            if ($value == 'Europa') {
                $europa = $this->europa_contries();
                echo $europa;
            } elseif ($value == 'Africa') {
                $africa = $this->africa_contries();
                echo $africa;
            } elseif ($value == 'Medio Oriente') {
                $middle_east = $this->middle_east();
                echo $middle_east;
            } elseif ($value == 'Lejano Oriente') {
                $far_east = $this->far_east();
                echo $far_east;
            } elseif ($value == 'España') {
                $est_esp = $this->prov_esp();
                echo $est_esp;
            } elseif ($value == 'Italia') {
                $est_ita = $this->prov_ita();
                echo $est_ita;
            }
        }
        wp_die();


    }

    public function my_script_footer_countries()
    {

        $this->ajax_data_countries('destino', 'select_country', 'carga');
    }


    public function ajax_data_countries($select, $action, $carga)
    {
        ?>
        <script>
            jQuery(document).ready(function () {
                jQuery('#<?php echo $select ?>').on('change', function () {
                    var value = jQuery('#<?php echo $select ?>').val();


                    //var ajaxurl = '/wp-admin/admin-ajax.php';

                    jQuery.ajax({
                        url: ajaxurl,
                        type: 'GET',
                        timeout: 5000,
                        dataType: 'html',
                        data: {action: '<?php echo $action ?>', value: value},
                        error: function (xml) {
                            //timeout, but no need to scare the user
                        },
                        success: function (response) {
                            if (response) {
                                jQuery('.select-dest').remove();
                                jQuery('#<?php echo $carga ?>').append(response);
                            }

                        }
                    })
                })
            })

        </script> <?php
    }
    public function ajax_contact(){
        ?>
        <script>
            jQuery(document).ready(function () {
                jQuery('#bookingformcontact').submit(function (event) {

                    event.preventDefault();
                    event.stopPropagation();


                    //var ajaxurl = '/wp-admin/admin-ajax.php';
                    form = jQuery('#bookingformcontact').serializeArray();

                    jQuery.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        timeout: 5000,
                        dataType: 'html',
                        data: {action: 'contact', form: form},
                        error: function (xml) {
                            //timeout, but no need to scare the user
                        },
                        success: function (response) {
                            console.log(response);
                            if (response == 1) {
                                jQuery('#content').after(
                                    '<div  class="col-xs-12 "><span id="baddate" class="alert alert-info">Has enviado un mensaje</span></div>');
                                jQuery("#bookingformcontact")[0].reset();

                                setTimeout(function () {
                                    jQuery('#baddate').remove()
                                }, 3000);
                            }


                        }
                    });
                })
            })

        </script>
        <?php

    }


    public function europa_contries()
    {
        $countries = '<div  class="select-dest">
                     <label for="pais" class="col-lg-2 control-label">País</label>
                         <div  class="col-lg-10">
                   <select  class="form-control select-dest" name="state" id="state" required>
                           <option value="">Seleccione</option>                           
                            <option value="Albania">Albania</option>
                            <option value="Andorra">Andorra</option>
                            <option value="Austria">Austria</option>
                            <option value="Belarus">Belarus</option>
                            <option value="Belgium">Belgium</option>
                            <option value="Bosnia">Bosnia and Herzegovina</option>
                            <option value="Bulgaria">Bulgaria</option>
                            <option value="Croatia">Croatia (Hrvatska)</option>                           
                            <option value="Czech R">Czech Republic</option>
                            <option value="France">France</option>
                            <option value="Gibraltar">Gibraltar</option>
                            <option value="Germany">Germany</option>
                            <option value="Greece">Greece</option>
                            <option value="Holy">Holy See (Vatican City State)</option>
                            <option value="Hungary">Hungary</option>
                            <option value="Italy">Italy</option>
                            <option value="Liechtenstein">Liechtenstein</option>
                            <option value="Luxembourg">Luxembourg</option>
                            <option value="Macedonia">Macedonia</option>
                            <option value="Malta">Malta</option>
                            <option value="Moldova">Moldova</option>
                            <option value="Monaco">Monaco</option>
                            <option value="Montenegro">Montenegro</option>
                            <option value="Netherlands">Netherlands</option>
                            <option value="Poland">Poland</option>
                            <option value="Portugal">Portugal</option>
                            <option value="Romania">Romania</option>
                            <option value="San Marino">San Marino</option>
                            <option value="Serbia">Serbia</option>
                            <option value="Slovakia">Slovakia</option>
                            <option value="Slovenia">Slovenia</option>
                            <option value="Spain">Spain</option>
                            <option value="Ukraine">Ukraine</option>
                            <option value="Denmark">Denmark</option>
                            <option value="Estonia">Estonia</option>
                            <option value="Faroe Islands">Faroe Islands</option>
                            <option value="Finland">Finland</option>
                            <option value="Greenland">Greenland</option>
                            <option value="Iceland">Iceland</option>
                            <option value="Ireland">Ireland</option>
                            <option value="Latvia">Latvia</option>
                            <option value="Lithuania">Lithuania</option>
                            <option value="Norway">Norway</option>
                            <option value="Russian">Russian Federation</option>
                            <option value="Svalbard">Svalbard and Jan Mayen Islands</option>
                            <option value="Sweden">Sweden</option>
                            <option value="Switzerland">Switzerland</option>
                            <option value="United Kingdom">United Kingdom</option>
                            
</select>';
        return $countries;
    }

    public function africa_contries()
    {
        $countries = '<div  class="select-dest">
              <label for="pais" class="col-lg-2 control-label">País</label>
                 <div  class="col-lg-10">
                 <select  class="form-control select-dest" name="state" id="state" required>
                             <option value="">Seleccione</option>
                            <option value="Algeria">Algeria</option>
                            <option value="Angola">Angola</option>
                            <option value="Benin">Benin</option>
                            <option value="Botswana">Botswana</option>
                            <option value="Burkina">Burkina Faso</option>
                            <option value="Burundi">Burundi</option>
                            <option value="Cameroon">Cameroon</option>
                            <option value="Cape Verde">Cape Verde</option>
                            <option value="Central African">Central African Republic</option>
                            <option value="Chad">Chad</option>
                            <option value="Comoros">Comoros</option>
                            <option value="Congo">Congo</option>
                            <option value="Congo, the Democratic Republic">Congo, the Democratic Republic of the</option>
                            <option value="Dijibouti">Dijibouti</option>
                            <option value="Egypt">Egypt</option>
                            <option value="Equatorial Guinea">Equatorial Guinea</option>
                            <option value="Eritrea">Eritrea</option>
                            <option value="Ethiopia">Ethiopia</option>
                            <option value="Gabon">Gabon</option>
                            <option value="Gambia">Gambia</option>
                            <option value="Ghana">Ghana</option>
                            <option value="Guinea">Guinea</option>
                            <option value="Guinea-Bissau">Guinea-Bissau</option>
                            <option value="Costa de Marfil">Cote d\'Ivoire (Ivory Coast)</option>
                            <option value="Kenya">Kenya</option>
                            <option value="Lesotho">Lesotho</option>
                            <option value="Liberia">Liberia</option>
                            <option value="Libya">Libya</option>
                            <option value="Madagascar">Madagascar</option>
                            <option value="Malawi">Malawi</option>
                            <option value="Mali">Mali</option>
                            <option value="Mauritania">Mauritania</option>
                            <option value="Mauritius">Mauritius</option>
                            <option value="Mayotte">Mayotte</option>
                            <option value="Morocco">Morocco</option>
                            <option value="Mozambique">Mozambique</option>
                            <option value="Namibia">Namibia</option>
                            <option value="Niger">Niger</option>
                            <option value="Nigeria">Nigeria</option>
                            <option value="Reunion">Reunion</option>
                            <option value="Rwanda">Rwanda</option>
                            <option value="Sao Tome">Sao Tome and Principe</option>
                            <option value="Saint Helena">Saint Helena</option>
                            <option value="Senegal">Senegal</option>
                            <option value="Seychelles">Seychelles</option>
                            <option value="Sierra Leone">Sierra Leone</option>
                            <option value="Somalia">Somalia</option>
                            <option value="South Africa">South Africa</option>
                            <option value="South Sudan">South Sudan</option>
                            <option value="Sudan">Sudan</option>
                            <option value="Swaziland">Swaziland</option>
                            <option value="Tanzania">Tanzania</option>
                            <option value="Togo">Togo</option>
                            <option value="Tunisia">Tunisia</option>
                            <option value="Uganda">Uganda</option>
                            <option value="Western Sahara">Western Sahara</option>
                            <option value="Zambia">Zambia</option>
                            <option value="Zimbabwe">Zimbabwe</option>
                            </select></div></div>';
        return $countries;
    }

    public function middle_east()
    {
        $countries = '<div  class="select-dest">
              <label for="pais" class="col-lg-2 control-label">País</label>
                 <div  class="col-lg-10">
<select  class="form-control select-dest" name="state" id="state" required>
                            <option value="">Seleccione</option>
                            <option value="Afganistan">Afganistan</option>
                            <option value="Armenia">Armenia</option>
                            <option value="Azerbaijan">Azerbaijan</option>
                            <option value="Bahrain">Bahrain</option>                            
                             <option value="Cyprus">Cyprus</option>                            
                            <option value="Iran">Iran</option>
                            <option value="Iraq">Iraq</option>
                            <option value="Israel">Israel</option>                           
                            <option value="Jordan">Jordan</option>
                            <option value="Kazakhstan">Kazakhstan</option>                           
                            <option value="Kuwait">Kuwait</option>
                            <option value="Kyrgyzstan">Kyrgyzstan</option>                  
                            <option value="Oman">Oman</option>
                            <option value="Pakistan">Pakistan</option>                   
                            <option value="Qatar">Qatar</option>                           
                            <option value="Saudi Arabia">Saudi Arabia</option>                  
                            <option value="Tajikistan">Tajikistan</option>
                            <option value="Turkey">Turkey</option>                  
                            <option value="Turkmenistan">Turkmenistan</option>
                            <option value="United Arab Emirates">United Arab Emirates</option>
                            <option value="Uzbekistan">Uzbekistan</option>                          
                             <option value="Yemen">Yemen</option>
           </select></div></div>';
        return $countries;
    }

    public function far_east()
    {
        $countries = '
              <div class="select-dest">
              <label for="pais" class="col-lg-2 control-label">País</label>
                 <div  class="col-lg-10">
                  <select  class="form-control select-dest" name="state" id="state" required>                                         
                            <option value="">Seleccione</option>
                            <option value="Bangladesh">Bangladesh</option>
                            <option value="Bhutan">Bhutan</option>
                            <option value="British Indian Ocean Territory">British Indian Ocean Territory</option>
                            <option value="Brunei Darussalam">Brunei Darussalam</option>
                            <option value="Cambodia">Cambodia</option>
                            <option value="China">China</option>
                            <option value="Christmas Island">Christmas Island</option>
                            <option value="Cocos (Keeling) Islands">Cocos (Keeling) Islands</option>
                            <option value="Georgia">Georgia</option>
                            <option value="Hong Kong">Hong Kong</option>
                            <option value="India">India</option>
                            <option value="Indonesia">Indonesia</option>                           
                            <option value="Japan">Japan</option>                        
                            <option value="Korea, Democratic Peoples Republic of">Korea, Democratic Peoples Republic of</option>
                            <option value="Sur Korea">Sur Korea</option>                          
                            <option value="Lao">Lao</option>                            
                            <option value="Malaysia">Malaysia</option>
                            <option value="Maldives">Maldives</option>
                            <option value="Mongolia">Mongolia</option>
                            <option value="Myanmar (Burma)">Myanmar (Burma)</option>
                            <option value="Nepal">Nepal</option>                 
                            <option value="Philippines">Philippines</option>                 
                            <option value="Singapore">Singapore</option>
                            <option value="Sri Lanka">Sri Lanka</option>                           
                            <option value="Taiwan">Taiwan</option>                           
                            <option value="Thailand">Thailand</option>
                            <option value="East Timor">East Timor</option>                 
                            <option value="Vietnam">Vietnam</option>                            
                             </select></div></div>';
        return $countries;
    }

    public function prov_esp()
    {
        $countries = '
              <div class="select-dest">
              <label for="provincias" class="col-lg-2 control-label">Provincias</label>
                 <div  class="col-lg-10">
                  <select  class="form-control select-dest" name="state" id="state" required>
                                       	<option value="">Seleccione</option>
                                        <option value="Álava">Álava</option>
                                        <option value="Albacete">Albacete</option>
                                        <option value="Alicante">Alicante/Alacant</option>
                                        <option value="Almería">Almería</option>
                                        <option value="Asturias">Asturias</option>
                                        <option value="Ávila">Ávila</option>
                                        <option value="Badajoz">Badajoz</option>
                                        <option value="Barcelona">Barcelona</option>
                                        <option value="Burgos">Burgos</option>
                                        <option value="Cáceres">Cáceres</option>
                                        <option value="Cádiz">Cádiz</option>
                                        <option value="Cantabria">Cantabria</option>
                                        <option value="Castellón">Castellón/Castelló</option>
                                        <option value="Ceuta">Ceuta</option>
                                        <option value="Ciudad Real">Ciudad Real</option>
                                        <option value="Córdoba">Córdoba</option>
                                        <option value="Cuenca">Cuenca</option>
                                        <option value="Girona">Girona</option>
                                        <option value="Las Palmas">Las Palmas</option>
                                        <option value="Granada">Granada</option>
                                        <option value="Guadalajara">Guadalajara</option>
                                        <option value="Guipúzcoa">Guipúzcoa</option>
                                        <option value="Huelva">Huelva</option>
                                        <option value="Huesca">Huesca</option>
                                        <option value="Illes Balears">Illes Balears</option>
                                        <option value="Jaén">Jaén</option>
                                        <option value="A Coruña">A Coruña</option>
                                        <option value="La Rioja">La Rioja</option>
                                        <option value="León">León</option>
                                        <option value="Lleida">Lleida</option>
                                        <option value="Lugo">Lugo</option>
                                        <option value="Madrid">Madrid</option>
                                        <option value="Málaga">Málaga</option>
                                        <option value="Melilla">Melilla</option>
                                        <option value="Murcia">Murcia</option>
                                        <option value="Navarra">Navarra</option>
                                        <option value="Ourense">Ourense</option>
                                        <option value="Palencia">Palencia</option>
                                        <option value="Pontevedra">Pontevedra</option>
                                        <option value="Salamanca">Salamanca</option>
                                        <option value="Segovia">Segovia</option>
                                        <option value="Sevilla">Sevilla</option>
                                        <option value="Soria">Soria</option>
                                        <option value="Tarragona">Tarragona</option>
                                        <option value="Tenerife">Santa Cruz de Tenerife</option>
                                        <option value="Teruel">Teruel</option>
                                        <option value="Toledo">Toledo</option>
                                        <option value="Valencia">Valencia/Valéncia</option>
                                        <option value="Valladolid">Valladolid</option>
                                        <option value="Vizcaya">Vizcaya</option>
                                        <option value="Zamora">Zamora</option>
                                        <option value="Zaragoza">Zaragoza</option>                          
                             </select></div></div>';
        return $countries;
    }

    public function prov_ita()
    {
        $countries = '
              <div class="select-dest">
              <label for="provincias" class="col-lg-2 control-label">Provincias</label>
                 <div  class="col-lg-10">
                  <select  class="form-control select-dest" name="state" id="state" required>
                                       	<option value="" selected>Seleccione</option>
                                            <option value="Agrigento">Agrigento</option>
                                            <option value="Alessandria">Alessandria</option>
                                            <option value="Ancona">Ancona</option>
                                            <option value="Aosta">Aosta</option>
                                            <option value="Arezzo">Arezzo</option>
                                            <option value="Ascoli Piceno">Ascoli Piceno</option>
                                            <option value="Asti">Asti</option>
                                            <option value="Avellino">Avellino</option>
                                            <option value="Bari">Bari</option>
                                            <option value="Belluno">Belluno</option>
                                            <option value="Benevento">Benevento</option>
                                            <option value="Bergamo">Bergamo</option>
                                            <option value="Biella">Biella</option>
                                            <option value="Bologna">Bologna</option>
                                            <option value="Bolzano">Bolzano</option>
                                            <option value="Brescia">Brescia</option>
                                            <option value="Brindisi">Brindisi</option>
                                            <option value="Cagliari">Cagliari</option>
                                            <option value="Caltanissetta">Caltanissetta</option>
                                            <option value="Campobasso">Campobasso</option>
                                            <option value="Caserta">Caserta</option>
                                            <option value="Catania">Catania</option>
                                            <option value="Catanzaro">Catanzaro</option>
                                            <option value="Chieti">Chieti</option>
                                            <option value="Como">Como</option>
                                            <option value="Cosenza">Cosenza</option>
                                            <option value="Cremona">Cremona</option>
                                            <option value="Crotone">Crotone</option>
                                            <option value="Cuneo">Cuneo</option>
                                            <option value="Enna">Enna</option>
                                            <option value="Ferrara">Ferrara</option>
                                            <option value="Firenze">Firenze</option>
                                            <option value="Foggia">Foggia</option>
                                            <option value="Cesena"> Cesena</option>
                                            <option value="Frosinone">Frosinone</option>
                                            <option value="Genova">Genova</option>
                                            <option value="Gorizia">Gorizia</option>
                                            <option value="Grosseto">Grosseto</option>
                                            <option value="Imperia">Imperia</option>
                                            <option value="Isernia">Isernia</option>
                                            <option value="La Spezia">La Spezia</option>
                                            <option value="LAquila">LAquila</option>
                                            <option value="Latina">Latina</option>
                                            <option value="Lecce">Lecce</option>
                                            <option value="Lecco">Lecco</option>
                                            <option value="Livorno">Livorno</option>
                                            <option value="Lodi">Lodi</option>
                                            <option value="Lucca">Lucca</option>
                                            <option value="Macerata">Macerata</option>
                                            <option value="Mantova">Mantova</option>
                                            <option value="Massa Carrara">Massa Carrara</option>
                                            <option value="Matera">Matera</option>
                                            <option value="Messina">Messina</option>
                                            <option value="Milano">Milano</option>
                                            <option value="Modena">Modena</option>
                                            <option value="Napoli">Napoli</option>
                                            <option value="Novara">Novara</option>
                                            <option value="NU">Nuoro</option>
                                            <option value="Oristano">Oristano</option>
                                            <option value="Padova">Padova</option>
                                            <option value="Palermo">Palermo</option>
                                            <option value="Parma">Parma</option>
                                            <option value="Pavia">Pavia</option>
                                            <option value="Perugia ">Perugia</option>
                                            <option value="Pesaro">Pesaro</option>
                                            <option value="Pescara">Pescara</option>
                                            <option value="Piacenza">Piacenza</option>
                                            <option value="Pisa">Pisa</option>
                                            <option value="Pistoia">Pistoia</option>
                                            <option value="Pordenone">Pordenone</option>
                                            <option value="Potenza">Potenza</option>
                                            <option value="Prato">Prato</option>
                                            <option value="Ragusa">Ragusa</option>
                                            <option value="Ravenna">Ravenna</option>
                                            <option value="Reggio Calabria">Reggio Calabria</option>
                                            <option value="Reggio Emilia">Reggio Emilia</option>
                                            <option value="Rieti">Rieti</option>
                                            <option value="Rieti">Rimini</option>
                                            <option value="Roma">Roma</option>
                                            <option value="Rovigo">Rovigo</option>
                                            <option value="Salerno">Salerno</option>
                                            <option value="Sassari">Sassari</option>
                                            <option value="Savona">Savona</option>
                                            <option value="Siena">Siena</option>
                                            <option value="Siracusa">Siracusa</option>
                                            <option value="Sondrio">Sondrio</option>
                                            <option value="Taranto">Taranto</option>
                                            <option value="Teramo">Teramo</option>
                                            <option value="Terni">Terni</option>
                                            <option value="Torino">Torino</option>
                                            <option value="Trapani">Trapani</option>
                                            <option value="Trento">Trento</option>
                                            <option value="Treviso">Treviso</option>
                                            <option value="Trieste">Trieste</option>
                                            <option value="Udine">Udine</option>
                                            <option value="Varese">Varese</option>
                                            <option value="Venezia">Venezia</option>
                                            <option value="Verbania">Verbania-Cusio-Ossola</option>
                                            <option value="Vercelli">Vercelli</option>
                                            <option value="Verona">Verona</option>
                                            <option value="Vibo Valentia">Vibo Valentia</option>
                                            <option value="Valentia">Vicenza</option>
                                            <option value="Viterbo">Viterbo</option>                    
                             </select></div></div>';
        return $countries;
    }

    public function all_mails()
    {
        $args = array("post_type" => "reservaciones",
            "post_status" => "publish",
            "posts_per_page" => -1,
            "order" => "ASC");
        $reservaciones = get_posts($args);
        $emails = array();


        foreach ($reservaciones as $reservacion):setup_postdata($reservacion);

            $emails[] = get_post_meta($reservacion->ID, "email");
        endforeach;
        wp_reset_postdata();
        return $emails;
    }

    public function my_booking_shortcode_cart()
    {
        $form = '<section id="' . get_the_title() . '">
                
                                        <div class="row">

                                            <div class="col-xs-12">
                                                <form id="bookingformcart" class="form-horizontal" role="form" action="' . home_url('/') . '" 
                                                      method="post">
                                                    <div class="col-xs-12">
                                                     <div class="col-xs-12 text-center">
                                                            <h3>Datos de Renta</h3> 
                                                              </div>
                                                       <div class="form-group">
                                                            <label for="destino" class="col-lg-2 control-label">Destino</label>

                                                            <div class="col-lg-10">
                                                       ' . $this->my_destino_select() . '
                                                                
                                                            </div>                                                            
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="destino" class="col-lg-2 control-label">Autos</label>

                                                            <div class="col-lg-10">
                                                       ' . $this->my_autos() . '
                                                                
                                                            </div>                                                            
                                                        </div>
                                                        
                                                        
                                                        
                                                        <div class="form-group">
                                                             <label for="fecha_inicial" class="col-lg-2 control-label">Fecha de Recogida</label>
                                                             <div class="col-lg-10">
                                                             <input type="text" class="form-control" required name="startdate" id="startdate"
                                                                       value=""
                                                                       placeholder="Fecha de Recogida">
                                                             </div>
                                                             </div>
                                                             <div class="form-group">
                                                             <label for="lugar_recogida" class="col-lg-2 control-label">Lugar de Recogida</label>
                                                             <div class="col-lg-10">
                                                             <input type="text" class="form-control" required name="lugar_recogida" id="lugar_recogida"
                                                                       value=""
                                                                       placeholder="Lugar de Recogida">
                                                               </div>
                                                             </div>
                                                             
                                                             <div class="form-group">
                                                             <label for="fecha_final" class="col-lg-2 control-label">Fecha de devolución</label>
                                                             <div class="col-lg-10">
                                                             <input type="text" class="form-control" required name="finaldate" id="finaldate"
                                                              value="" placeholder="Fecha de devolución">
                                                             </div>
                                                            </div>
                                                            <div class="form-group">
                                                             <label for="lugar_devolucion" class="col-lg-2 control-label">Lugar de Devolución</label>
                                                             <div class="col-lg-10">
                                                             <input type="text" class="form-control" required name="lugar_devolucion" id="lugar_devolucion"
                                                                       value=""
                                                                       placeholder="Lugar de Devolución">
                                                               </div>
                                                             </div>
                                                             <div class="form-group">
                                                             <label for="numero_vuel" class="col-lg-2 control-label">Número de Vuelo</label>
                                                             <div class="col-lg-10">
                                                             <input type="text" class="form-control" required name="numero_vuel" id="numero_vuel"
                                                                       value=""
                                                                       placeholder="Número de Vuelo">
                                                               </div>
                                                             </div>
                                                              <div class="form-group">
                                                             <label for="hora_lleg" class="col-lg-2 control-label">Hora de Llegada</label>
                                                             <div class="col-lg-10">
                                                             <input type="text" class="form-control" required name="hora_lleg" id="hora_lleg"
                                                                       value=""
                                                                       placeholder="Hora de Llegada">
                                                               </div>
                                                             </div>';
        $form .= $this->datos_personales();
        $form .= $this->mycapcha_field();
        $form .= '<div class=" modal-footer">                                  
                                                                <button type="submit"    id="buttonsubmit" class="btn btn-primary">Reserve ahora</button>
                                                            </div>
                                                                                                                         
                                                            </form>
                                                        </div>
                                                        </div>
                                                        
                                                       
                                                        </section>';


        $form .= $this->myscript_datetimepicker("startdate", "finaldate");
        $form .= $this->hora_datetimepicker("hora_lleg");
        $form .= $this->nac_datetimepicker("fecha_nac");
        //$form .= $this->select2("select2");
        echo $form;


    }

    public function my_autos()
    {
        $args = array("post_type" => "autos",
            "post_status" => "publish",
            "posts_per_page" => -1,
            "order" => "ASC");
        $autos = get_posts($args);
        $select = '<select class="form-control" name="autos_renta" id="autos_renta">';
        $select .= '<option value="select">seleccione</option>';
        foreach ($autos as $post):setup_postdata($post);
            $select .= '<option value="' . $post->post_title . '">' . $post->post_title . '</option>';
        endforeach;
        $select .= '</select>';
        wp_reset_postdata();
        return $select;

    }

    public function hora_datetimepicker($fecha)
    {
        $form = '<script type="text/javascript">
    jQuery(document).ready(function () {
        jQuery("#' . $fecha . '").datetimepicker({format: "LT",minDate:"now"});
                 
                        })
                
                      </script>';
        return $form;

    }

    public function destino_int()
    {
        $args = array("post_type" => "internacionales",
            "post_status" => "publish",
            "posts_per_page" => -1,
            "order" => "ASC");
        $autos = get_posts($args);
        $select = '<select class="form-control" name="destino" id="destino" required>';
        $select .= '<option value="">seleccione</option>';
        foreach ($autos as $post):setup_postdata($post);
            $select .= '<option value="' . $post->post_title . '">' . $post->post_title . '</option>';
        endforeach;
        $select .= '</select>';
        wp_reset_postdata();
        return $select;

    }


    public function my_booking_shortcode_hotel()
    {
        $form = '<section id="' . get_the_title() . '">
                <div class="col-xs-12">                           
                                    <section id="formulario">
                                        <div class="row">

                                            <div class="col-xs-12">
                                                <form id="bookingformhotel" class="form-horizontal" role="form" action="' . home_url('/') . '" 
                                                      method="post">
                                                    <div class="col-xs-12">
                                                     <div class="col-xs-12 text-center">
                                                            <h3>Datos del Viaje</h3> 
                                                              </div>
                                                       <div class="form-group">
                                                            <label for="destino" class="col-lg-2 control-label">Destino</label>

                                                            <div class="col-lg-10">
                                                            ' . $this->my_destino_select() . '
                                                                
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                             <label for="fecha_inicial" class="col-lg-2 control-label">Fecha Inicial</label>
                                                             <div class="col-lg-10">
                                                             <input type="text" class="form-control" required name="startdate" id="startdate"
                                                                       value=""
                                                                       placeholder="Fecha Inicial">
                                                             </div>
                                                             </div>
                                                             <div class="form-group">
                                                             <label for="fecha_final" class="col-lg-2 control-label">Fecha Final</label>
                                                             <div class="col-lg-10">
                                                             <input type="text" class="form-control" required name="finaldate" id="finaldate"
                                                              value="" placeholder="Fecha Final">
                                                             </div>
                                                             </div>
                                                             <div class="form-group">
                                                             <label for="hoteles" class="col-lg-2 control-label">Hoteles</label>
                                                             <div class="col-lg-10">
                                                             ' . $this->my_hoteles() . '
                                                             </div>
                                                             </div>
                                                             <div class="form-group">
                                                             <label for="cant_pers" class="col-lg-2 control-label">Cantidad de Personas</label>
                                                             <div class="col-lg-10">
                                                             <input type="text" class="form-control" required name="cant_pers" id="cant_pers"
                                                              value="" placeholder="Cantidad de Personas">  
                                                             </div>
                                                             </div>
                                                             <div class="form-group">             
                                                             
                                                             <div class="col-xs-10 alert alert-info">
                                                             <p>agregue los datos de las personas que le acompañaran</p>                                                             
                                                             </div>
                                                             </div>
                                                             <div id="divpers"></div>
                                                             ';
        $form .= $this->datos_personales();
        $form .= $this->mycapcha_field();
        $form .= $this->myscript_datetimepicker("startdate", "finaldate");
        $form .= $this->add_persona();
        $form .= $this->nac_datetimepicker("fecha_nac");
        $form .= '<div class=" modal-footer">                                  
                                                                <button type="submit"    id="buttonsubmit" class="btn btn-primary">Reserve ahora</button>
                                                            </div>
                                                                                                                         
                                                            </form>
                                                        </div>
                                                        </div>
                                                        
                                                       
                                                        </section>';

        echo $form;


    }

    public function my_booking_shortcode_internacional()
    {
        $form = '<section id="' . get_the_title() . '">
                <div class="col-xs-12">                           
                                    <section id="formulario">
                                        <div class="row">

                                            <div class="col-xs-12">
                                                <form id="bookingforminternacional" class="form-horizontal" role="form" action="' . home_url('/') . '" 
                                                      method="post">
                                                    <div class="col-xs-12">
                                                     <div class="col-xs-12 text-center">
                                                            <h3>Datos del Viaje</h3> 
                                                              </div>
                                                       <div class="form-group">
                                                            <label for="destino" class="col-lg-2 control-label">Destino</label>

                                                            <div class="col-lg-10">
                                                            ' . $this->destino_int() . '                                                              
                                                            </div>
                                                        </div>
                                                        <div id="carga" class="form-group">
                                                            
                                                        </div>
                                                        <div class="form-group">
                                                             <label for="fecha_inicial" class="col-lg-2 control-label">Fecha Inicial</label>
                                                             <div class="col-lg-10">
                                                             <input type="text" class="form-control" required name="startdate" id="startdate"
                                                                       value=""
                                                                       placeholder="Fecha Inicial">
                                                             </div>
                                                             </div>
                                                             <div class="form-group">
                                                             <label for="fecha_final" class="col-lg-2 control-label">Fecha Final</label>
                                                             <div class="col-lg-10">
                                                             <input type="text" class="form-control" required name="finaldate" id="finaldate"
                                                              value="" placeholder="Fecha Final">
                                                             </div>
                                                             </div>
                                                             <div class="form-group">
                                                             <label for="hoteles" class="col-lg-2 control-label">Hoteles</label>
                                                             <div class="col-lg-10">
                                                             <input type="text" class="form-control" required name="hoteles_selc" id="hoteles_selc"
                                                              value="" placeholder="Hotel">
                                                             </div>
                                                             </div>
                                                             <div class="form-group">
                                                             <label for="cant_pers" class="col-lg-2 control-label">Cantidad de Personas</label>
                                                             <div class="col-lg-10">
                                                             <input type="text" class="form-control" required name="cant_pers" id="cant_pers"
                                                              value="" placeholder="Cantidad de Personas">              
                                                             
                                                             </div>
                                                             </div>
                                                             <div class="form-group">
                                                             
                                                             <div class="col-xs-10 alert alert-info">
                                                             <p>agregue los datos de las personas que le acompañaran</p>                                                             
                                                             </div>
                                                             </div>
                                                             <div id="divpers"></div>
                                                             ';
        $form .= $this->datos_personales();
        $form .= $this->mycapcha_field();
        $form .= $this->myscript_datetimepicker("startdate", "finaldate");
        $form .= $this->add_persona();
        $form .= $this->nac_datetimepicker("fecha_nac");
        $form .= '<div class=" modal-footer">                                  
                                                                <button type="submit"    id="buttonsubmit" class="btn btn-primary">Reserve ahora</button>
                                                            </div>
                                                                                                                         
                                                            </form>
                                                        </div>
                                                        </div>
                                                        
                                                       
                                                        </section>';

        echo $form;


    }

    public function my_shorcode_cotact()
    {
        $form = '<div class="col-xs-12">                           
                                    <section id="formulario">
                                        <div class="row">

                                            <div class="col-xs-12">
                                                <form id="bookingformcontact" class="form-horizontal" role="form" action="' . home_url('/') . '" 
                                                      method="post">
                                                    <div class="col-xs-12">
                                                     <div class="col-xs-12">
                                                            <h3>Formulario de Contacto</h3> 
                                                              </div>
                                                              </div>
                                                            <div class="form-group">
                                                            <label for="contactName" class="col-lg-2 control-label">Name</label>
                                                            <div class="col-lg-10">
                                                            <input  id ="contact_name"type="text" class="form-control" required name="contactName" value="" placeholder="Name">
                                                            </div>
                                                            </div>
                                                             <div class="form-group">
                                                             <label for="email" class="col-lg-2 control-label">Email</label>
                                                             <div class="col-lg-10">
                                                             <input type="email" class="form-control" required name="email" id="email"
                                                                       value=""  placeholder="Email">
                                                             </div>
                                                             </div>
                                                                                                                           <div class="form-group">
                                                             <label for="content" class="col-lg-2 control-label">Contenido</label>
                                                             <div class="col-lg-10">
                                                             <textarea id="content" name="content" placeholder="Escriba su Mensaje Aqui" required></textarea>
                                                             </div>
                                                             </div>
                                                             <div class=" modal-footer">                                  
                                                                <button type="submit"    id="buttonsubmit" class="btn btn-primary">ENVIAR</button>
                                                            </div>
                                                              </form>
                                                              </div></div></section></div>';
        echo $form;
    }

    public function my_hoteles()
    {
        $args = array("post_type" => "hoteles",
            "post_status" => "publish",
            "posts_per_page" => -1,
            "order" => "ASC");
        $hoteles = get_posts($args);
        $select = '<select class="form-control" name="hoteles_selc" id="hoteles_selc" required>';
        $select .= '<option value="select">seleccione</option>';
        foreach ($hoteles as $post):setup_postdata($post);
            $select .= '<option value="' . $post->post_title . '">' . $post->post_title . '</option>';
        endforeach;
        $select .= '</select>';
        wp_reset_postdata();
        return $select;

    }

    public function cantidad_pers()
    {
        $cant = '<select class="form-control" name="cant_pers" id="cant_pers" required>
                <option value="0">0</option>
                <option value="1">1</option>
                 <option value="2">2</option>
                  <option value="3">3</option>
                   <option value="4">4</option>
                    <option value="5">5</option>
                     <option value="6">6</option>
                      <option value="7">7</option>
                       <option value="8">8</option>
                       <option value="9">9</option>
                       <option value="10">10</option>
                       <option value="11">11</option>
                <option value="12">12</option>
                 <option value="13">13</option>
                  <option value="14">14</option>
                   <option value="15">15</option>
                    <option value="16">16</option>
                     <option value="17">17</option>
                      <option value="18">18</option>
                       <option value="19">19</option>
                       <option value="20">20</option>                      
                       </select>';
        return $cant;
    }

    public function add_persona()
    {
        ?>
        <script>
            function add_persona() {
                jQuery(document).ready(function () {
                    var count = 0;

                    count++;
                    var cant_pers = jQuery('#cant_pers').val();
                    if (cant_pers < 100) {
                        var cant = cant_pers - 1;
                    } else {
                        jQuery('#divpers').append(
                            '<div id="alert" class="alert alert-warning"><strong>Info!</strong>&nbsp;&nbsp;No le parece que son demasiados acompañantes</div>'
                        );
                        setTimeout(function () {
                            jQuery('#alert').remove()
                        }, 3000);
                    }

                    console.log(cant);
                    console.log(count);
                    if (cant < 0) {
                        jQuery('#divpers').append(
                            '<div id="alert" class="alert alert-warning"><strong>Info!</strong>&nbsp;&nbsp;Agregue al menos mas de una persona</div>'
                        );
                        setTimeout(function () {
                            jQuery('#alert').remove()
                        }, 3000);

                    }
                    else if (cant == 0) {
                        jQuery('#divpers').append(
                            '<div id="alert" class="alert alert-info"><strong>Info!</strong>&nbsp;&nbsp;Usted viaja solo no necesita agregar datos acompañantes</div>'
                        );
                        setTimeout(function () {
                            jQuery('#alert').remove()
                        }, 3000);
                    }
                    else {
                        var i = 0
                        while (i < cant) {

                            jQuery('#divpers').append(
                                '<div class="pers"><div class="form-group"><label for="acompte_nomb" class="col-lg-2 control-label">Nombre</label><div class="col-lg-10"><input type="text" class="form-control" required name="acompte_nomb' + i + '" id="acompte_nomb' + i + '"  value="" placeholder="Nombre"></div></div>\<' +
                                'div class="form-group"><label for="acompte_apell" class="col-lg-2 control-label">Apellido</label><div class="col-lg-10"><input type="text" class="form-control" required name="acompte_apell' + i + '" id="acompte_apell' + i + '"  value="" placeholder="Nombre"></div></div>\<' +
                                'div class="form-group"><label for="passp" class="col-lg-2 control-label">Pasaporte</label><div class="col-lg-10"><input type="text" class="form-control" required name="passp' + i + '" id="passp' + i + '"  value="" placeholder="Pasaporte"></div></div></div>'
                            );
                            i++;
                        }
                        // jQuery('#addpers').off('click');


                        // cant = cant - 1;


                    }

                    // console.log("el valor de "+count);
                    /* if(count == cant){
                     console.log("entre aki");
                     jQuery('#addpers').off('click');
                     }*/


                });
            }
            //add_persona()
            jQuery(document).ready(function () {
                jQuery('#cant_pers').on('change', function () {
                    jQuery('.pers').remove();
                    //jQuery('#addpers').off('click');

                    add_persona();

                });
            })


        </script>
    <?php }

}

