<?php
/**
 * Plugin Name: EK - Events
 * Plugin URI: http://decodekult.com
 * Description: Adds a new custom post type called "Events" to your WordPress site.
 * Author: Juan de Paco
 * Author URI: http://decodekult.com
 * Version: 0.1.0
 */

// Post-like & Taxonomy Objects

add_action( 'init', 'ek_e_register_objects');

function ek_e_register_objects() {
	 register_post_type('evento', array('labels' => array('name' => __('Eventos'),'singular_name' => __('Evento'),'add_new' => __('Añadir evento'),'all_items'=>__('Todos los eventos'),
			'add_new_item' => __('Añadir evento'),'edit_item' => __('Editar evento'),'new_item' => __('Nuevo evento'),'view_item' => __('Ver evento'),
			'search_items' => __('Buscar evento'),'not_found' =>  __('No se han encontrado eventos'),'not_found_in_trash' => __('No hay eventos en la papelera')),
		'public' => true,'publicly_queryable' => true,'show_ui' => true,'capability_type' => 'post','hierarchical' => false,'can_export'=>true,
		'query_var' => 'evento','supports' => array('title', 'editor' , 'thumbnail' , 'custom-fields'),'_builtin' => false,'_edit_link' => 'post.php?post=%d',
		'menu_position' => 4,'rewrite' => array( 'slug' => 'evento'), 'has_archive' => 'evento')
	);
	register_taxonomy('tipo-de-evento',array( 'evento' ),array(
		'labels' => array('name' => __( 'Tipos de eventos' ),'singular_name' => __( 'Tipo de eventos' ),'search_items' =>  __( 'Buscar tipos de evento' ),
			'all_items' => __( 'Todos los tipos de evento' ),'edit_item' => __( 'Editar tipo de evento' ),'update_item' => __( 'Guardar tipo de evento' ),
			'add_new_item' => __( 'Nuevo tipo de evento' ),'new_item_name' => __( 'Nuevo nombre de tipo de evento' )),
		'public' => true,'show_ui' => true,'hierarchical' => true,'rewrite' => array( 'slug' => 'tipo-de-evento'),'query_var' => 'tipo-de-evento')
	);
};

// Custom Meta-boxes

/**
 * Registering meta boxes
 *
 * In this file, I'll show you how to add more field type (in this case, the 'taxonomy' type)
 * All the definitions of meta boxes are listed below with comments, please read them CAREFULLY
 *
 * You also should read the changelog to know what has been changed
 *
 * For more information, please visit: http://www.deluxeblogtips.com/2010/04/how-to-create-meta-box-wordpress-post.html
 *
 */

/**
 * Prefix of meta keys (optional)
 * Wse underscore (_) at the beginning to make keys hidden
 * You also can make prefix empty to disable it
 */

$prefix = '_ek_';

$meta_boxes = array();

$meta_boxes[] = array(
	'id' => 'event-details',
	'title' => 'Detalles del evento',
	'pages' => array('evento'), // multiple post types
	'context' => 'side',
	'priority' => 'high',
	'fields' => array(
		array(
			'name' => 'Atemporal',
			'id' => $prefix . 'event_tipo',
			'type' => 'checkbox',
			'desc' => 'Dura mientras esté Publicado',
		),
		array(
			'name' => 'Fecha',
			'id' => $prefix . 'event_date',
			'type' => 'date',
			'format' => 'yy-mm-dd',					// date format, default yy-mm-dd. Optional. See more formats here: http://goo.gl/po8vf
			'class' => 'sixty',
			'desc' => 'de inicio, si aplicable',
		),
		array(
			'name' => 'Hora',
			'id' => $prefix . 'event_date_time',
			'type' => 'time',                // Field type: time
			'format' => 'hh:mm',
			'class' => 'fourty',
			'desc' => 'de inicio, id',
		),
		array(
			'name' => 'Fecha de cierre',
			'id' => $prefix . 'event_date_end',
			'type' => 'date',
			'format' => 'yy-mm-dd',					// date format, default yy-mm-dd. Optional. See more formats here: http://goo.gl/po8vf
			'class' => 'sixty',
			'desc' => '',
		),
		array(
			'name' => 'Hora de cierre',
			'id' => $prefix . 'event_date_end_time',
			'type' => 'time',                // Field type: time
			'format' => 'hh:mm',
			'class' => 'fourty',
			'desc' => '',
		),
		array(
			'name' => 'Dónde se realiza el evento',
			'id' => $prefix . 'event_place',
			'type' => 'text',
			'desc' => 'El nombre del lugar',
		),
		array(
			'name' => 'Lugar: enlace',
			'id' => $prefix . 'event_place_link',
			'type' => 'text',
			'desc' => 'El enlace en Google Maps al lugar',
		),
	)
);

$meta_boxes[] = array(
	'id' => 'ad-hoc-post',
	'title' => 'Entrada relacionada',
	'pages' => array('evento', 'product'), // multiple post types
	'context' => 'side',
	'priority' => 'high',
	'fields' => array(
		array(
			'name' => 'ID de la(s) entrada(s)',
			'desc' => 'Escribe uno o varios IDs de entradas relevantes, separados con comas',
			'id' => $prefix . 'relevant_posts',
			'type' => 'text',
		),
	)
);

if (class_exists(RW_Meta_Box)) {
	foreach ($meta_boxes as $meta_box) {$my_box = new RW_Meta_Box($meta_box);}
}

// Custom Styles

function ek_e_styles() {
  echo '<link rel="stylesheet" type="text/css" media="all" href="'.get_bloginfo('url').'/wp-content/mu-plugins/events.css" />';
}
if ( !defined( 'IFRAME_REQUEST' ) ) {add_action('admin_head', 'ek_e_styles');}

// Custom Dashboard

// Custom Columns - Events

add_filter("manage_edit-evento_columns", "ek_evento_columns");
add_action("manage_posts_custom_column",  "ek_global_events_columns");

function ek_evento_columns($columns) { $columns = array(
		"cb" => "<input type=\"checkbox\" />",
		"title" => "Nombre",
		"tipoev" => "Tipo",
		"thing_id" => "eventID",
		"fecha" => "Cuándo",
		"lugar" => "Dónde",
		"date" => "Creado",
		);
	return $columns;
}

function ek_global_events_columns($column){
	global $post;$custom = get_post_custom();
	switch ($column){
		case "tipoev":
			echo get_the_term_list($post->ID, 'tipo-de-evento', '', ', ','');
			break;
		case 'thing_id':
			echo '#<strong>', $post->ID , '</strong>';
			break;
		case "fecha":
			if (isset($custom["_ek_event_tipo"]) && 0 != $custom["_ek_event_tipo"][0]) { echo '<p><strong>Tipo <em>atemporal</em></strong><br />Desde el ' , get_the_date("D, d M Y") , '</p>';}
			else {
				if (isset($custom["_ek_event_date"]) && isset($custom["_ek_event_date_end"])) {
					$desdel = $custom["_ek_event_date"][0];$stampini = strtotime($desdel);$hastal = $custom["_ek_event_date_end"][0];$stampfin = strtotime($hastal);
					echo '<p><strong>Desde el</strong> ' , date("d m Y", $stampini) , isset($custom["_ek_event_date_time"]) ? ' a las ' . $custom["_ek_event_date_time"][0] : '';
					echo '<br /><strong>Hasta el</strong> ' , date("d m Y", $stampfin) , isset($custom["_ek_event_date_end_time"]) ? ' a las ' . $custom["_ek_event_date_end_time"][0] : '';
					$sta = ek_compare_date($custom["_ek_event_date"][0], $custom["_ek_event_date_end"][0]);
					echo '<br /><strong>Estado</strong> <span class="staspan '. $sta . '"></span>'. $sta . '</p>';

				} elseif (isset($custom["_ek_event_date"])) {
					$desdel = $custom["_ek_event_date"][0];$stamp = strtotime($desdel);
					echo '<p>El <strong>' , date("D, d M Y", $stamp) , '</strong>', isset($custom["_ek_event_date_time"]) ? ' a las ' . $custom["_ek_event_date_time"][0] : '';
					$sta = ek_compare_date($custom["_ek_event_date"][0]);
					echo '<br /><strong>Estado</strong> <span class="staspan '. $sta . '"></span>'. $sta . '</p>';

				} else {
					echo '<p># No data</p>';
				}
			}
			break;
		case "lugar":
			if (isset($custom["_ek_event_place"])) {
				if (isset($custom["_ek_event_place_link"])) {$prea = '<a href="' . $custom["_ek_event_place_link"][0] . '" title="Ver en Google Maps" class="event-place">';$posta = '</a>';} else {$prea = '';$posta = '';};
				echo '<span class="green">' . $prea . $custom["_ek_event_place"][0] . $posta . '</span>';
			} else { echo '<span class="red"># No data</span>';};
			break;

	}
}

function ek_compare_date($event_date_start, $event_date_end = null) {
$days_to_start = strcmp($event_date_start, date("Y-m-d"));
$days_to_end = isset($event_date_end) ? strcmp($event_date_end, date("Y-m-d")) : $days_to_start;
if ($days_to_end < 0) {
	$estat = 'pasado';
} elseif ($days_to_start > 0) {
	$estat = 'futuro'; 
} else {
	$estat = 'ahora';
}
return $estat;;
}

?>