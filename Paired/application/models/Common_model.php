<?php
class Common_model extends CI_Model {

    // insert function
	public function insert($data,$table){
        $data = $this->security->xss_clean($data);
        $this->db->insert($table,$data);        
        return $this->db->insert_id();
    }

    // edit function
    function edit_option($action, $id, $table){
        $action = $this->security->xss_clean($action);
        $this->db->where('id',$id);
        $this->db->update($table,$action);
        return;
    } 

    // edit function
    function edit_option_md5($action, $id, $table){
        $action = $this->security->xss_clean($action);
        $this->db->where('md5(id)', $id);
        $this->db->update($table,$action);
        return;
    } 

    // update function
    function update($action,$id,$table){
        $action = $this->security->xss_clean($action);
        $this->db->where('id',$id);
        $this->db->update($table,$action);
    }

    // delete function
    function delete($id,$table){
        if (settings()->type == 'live') {
            $this->db->delete($table, array('id' => $id));
        }
        return;
    }

    function favourite_delete($id,$table){
        if (settings()->type == 'live') {
            $this->db->delete($table, array('md5(id)' => $id));
        }
        return;
    }

  

    // get function
    function get($table)
    {
        $this->db->select();
        $this->db->from($table);
        $this->db->order_by('id','DESC');
        $query = $this->db->get();
        $query = $query->row();  
        return $query;
    }

    function get_all($table)
    {
        $this->db->select();
        $this->db->from($table);
        $this->db->order_by('id','DESC');
        $query = $this->db->get();
        $query = $query->result();  
        return $query;
    }


    // select by function
    function select_by_user($table)
    {
        $this->db->select();
        $this->db->from($table);
        $this->db->where('user_id', $this->session->userdata('id'));
        $this->db->order_by('user_id','DESC');
        $query = $this->db->get();
        $query = $query->result();  
        return $query;
    }

    // select function
    function select($table)
    {
        $this->db->select();
        $this->db->from($table);
        $this->db->order_by('id','ASC');
        $query = $this->db->get();
        $query = $query->result();  
        return $query;
    }

    function get_service_categories($id)
    {
        $this->db->select('s.category_id, c.name');
        $this->db->from('services as s');
        $this->db->where('s.business_id',$id);
        $this->db->join('categories as c','s.category_id=c.id','LEFT');
        $this->db->group_by('s.category_id');
        $query = $this->db->get();
        $query = $query->result();
        foreach ($query as $key => $value) {
            $this->db->select('e.*');
            $this->db->from('services as e');
            $this->db->where('e.category_id',$value->category_id);
            $query2 = $this->db->get();
            $query2 = $query2->result();
            $query[$key]->services = $query2;
        }      
        return $query;
    }

    function get_portfolio_categories($id)
    {
        $this->db->select('p.category_id, c.name');
        $this->db->from('portfolios as p');
        $this->db->where('p.business_id',$id);
        $this->db->join('categories as c','p.category_id=c.id','LEFT');
        $this->db->group_by('p.category_id');
        $query = $this->db->get();
        $query = $query->result();
        foreach ($query as $key => $value) {
            $this->db->select('e.*');
            $this->db->from('portfolios as e');
            $this->db->where('e.category_id',$value->category_id);
            $query2 = $this->db->get();
            $query2 = $query2->result();
            $query[$key]->portfolios = $query2;
        }      
        return $query;
    }

    function get_product_categories($id)
    {
        $this->db->select('p.category_id, c.name');
        $this->db->from('products as p');
        $this->db->where('p.business_id',$id);
        $this->db->join('categories as c','p.category_id=c.id','LEFT');
        $this->db->group_by('p.category_id');
        $query = $this->db->get();
        $query = $query->result();
        foreach ($query as $key => $value) {
            $this->db->select('e.*');
            $this->db->from('products as e');
            $this->db->where('e.category_id',$value->category_id);
            $query2 = $this->db->get();
            $query2 = $query2->result();
            $query[$key]->products = $query2;
        }      
        return $query;
    }

    // function get_product_subcategories($status)
    // {
    //     $this->db->select('p.category_id, c.name');
    //     $this->db->from('products as p');
    //     $this->db->where('p.status',$status);
    //     $this->db->join('categories as c','p.category_id=c.id','LEFT');
    //     $this->db->group_by('p.category_id');
    //     $query = $this->db->get();
    //     $query = $query->result();
    //     foreach ($query as $key => $value) {
    //         $this->db->select('e.*');
    //         $this->db->from('products as e');
    //         $this->db->where('e.category_id',$value->category_id);
    //         $query2 = $this->db->get();
    //         $query2 = $query2->result();
    //         $query[$key]->products = $query2;
    //     }
    //     foreach ($query2 as $key => $value2) {
    //         $this->db->select('ep.*');
    //         $this->db->from('products as ep');
    //         $this->db->where('ep.subcategory_id',$value2->subcategory_id);
    //         $query3 = $this->db->get();
    //         $query3 = $query3->result();
    //         $query[$key]->sub_products = $query3;
    //     }      
    //     return $query;
    // }


    function get_product_order_lists($order_id)
    {
        $this->db->select('pl.*, p.title');
        $this->db->from('product_order_lists as pl');
        $this->db->join('products as p','p.id=pl.product_id','LEFT');
        $this->db->where('pl.order_id',$order_id);
        $query = $this->db->get();
        $query = $query->result();
        return $query;
    }


    function get_event_categories($id)
    {
        $this->db->select('e.category_id, c.name');
        $this->db->from('events as e');
        $this->db->where('e.business_id',$id);
        $this->db->join('categories as c','e.category_id=c.id','LEFT');
        $this->db->group_by('e.category_id');
        $query = $this->db->get();
        $query = $query->result();
        foreach ($query as $key => $value) {
            $this->db->select('p.*');
            $this->db->from('events as p');
            $this->db->where('p.category_id',$value->category_id);
            $query2 = $this->db->get();
            $query2 = $query2->result();
            $query[$key]->events = $query2;
        }      
        return $query;
    }

    // asc select function
    function select_asc($table)
    {
        $this->db->select();
        $this->db->from($table);
        $this->db->order_by('id','ASC');
        $query = $this->db->get();
        $query = $query->result();  
        return $query;
    }

    // asc select function
    function select_orders($table)
    {
        $this->db->select();
        $this->db->from($table);
        $this->db->order_by('orders','ASC');
        $query = $this->db->get();
        $query = $query->result();  
        return $query;
    }

    // select by id
    function select_option($id,$table)
    {
        $this->db->select();
        $this->db->from($table);
        $this->db->where('id', $id);
        $query = $this->db->get();
        $query = $query->result_array();  
        return $query;
    } 

    // select by id
    function get_by_id($id,$table)
    {
        $this->db->select();
        $this->db->from($table);
        $this->db->where('id', $id);
        $query = $this->db->get();
        $query = $query->row();  
        return $query;
    } 


    // select by id
    function get_by_uid($id,$table)
    {
        $this->db->select();
        $this->db->from($table);
        $this->db->where('uid', $id);
        $query = $this->db->get();
        $query = $query->row();  
        return $query;
    } 


    // select by id
    function get_by_md5_id($id,$table)
    {
        $this->db->select();
        $this->db->from($table);
        $this->db->where('md5(id)', $id);
        $query = $this->db->get();
        $query = $query->row();  
        return $query;
    }


    // select by slug
    function get_by_slug($slug,$table)
    {
        $this->db->select();
        $this->db->from($table);
        $this->db->where('slug', $slug);
        $query = $this->db->get();
        $query = $query->row();  
        return $query;
    }


    function get_cart_product($id)
    {
        $this->db->select();
        $this->db->from('products');
        $this->db->where('id', $id);
        $query = $this->db->get();
        $query = $query->row();  
        return $query;
    }  

    // select by slug
    function get_slug_by_company($slug, $table, $business_id)
    {
        $this->db->select();
        $this->db->from($table);
        $this->db->where('slug', $slug);
        $this->db->where('business_id', $business_id);
        $query = $this->db->get();
        $query = $query->row();  
        return $query;
    } 

    // select by slug
    function get_id_by_company($id, $table, $business_id)
    {
        $this->db->select();
        $this->db->from($table);
        $this->db->where('id', $id);
        $this->db->where('business_id', $business_id);
        $query = $this->db->get();
        $query = $query->row();  
        return $query;
    } 

    // select by slug
    function get_by_company($business_id, $table)
    {
        $this->db->select();
        $this->db->from($table);
        $this->db->where('business_id', $business_id);
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get();
        $query = $query->result();  
        return $query;
    }


    function get_data($uid, $table)
    {
        $this->db->select();
        $this->db->from($table);
        $this->db->where('business_id', $uid);
        $this->db->where('status', 1);
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get();
        $query = $query->result();  
        return $query;
    }


    function get_home_team($id,$limit)
    {
        $this->db->select();
        $this->db->from('teams');
        $this->db->where('business_id', $id);
        $this->db->where('status', 1);
        $this->db->order_by('id', 'DESC');
        $this->db->limit($limit);
        $query = $this->db->get();
        $query = $query->result();  
        return $query;
    }

    function get_pagination($id, $table, $total, $limit, $offset)
    {
        $this->db->select();
        $this->db->from($table);
        $this->db->where('business_id', $id);
        $this->db->where('status =',1);
        if ($total == 1) {
            $query = $this->db->get();
            $query = $query->num_rows();  
        } else {
            $query = $this->db->get('', $limit, $offset);
            $query = $query->result();  
        }
        return $query;
    }



    function get_blogs($id, $table, $total, $limit, $offset)
    {
        $this->db->select('b.*,c.name as category_name');
        $this->db->from('blogs as b');
        $this->db->join('categories as c','b.category_id=c.id','LEFT');
        $this->db->where('b.business_id', $id);
        $this->db->where('b.status =',1);
        if(!empty($_GET['category'])) {
            $this->db->where('b.category_id',$_GET['category']);
        }
        if ($total == 1) {
            $query = $this->db->get();
            $query = $query->num_rows();  
        } else {
            $query = $this->db->get('', $limit, $offset);
            $query = $query->result();  
        }
        return $query;
    }



    function get_products($id, $total, $limit, $offset)
    {
        $this->db->select('p.*');
        $this->db->from('products p');
        $this->db->where('p.business_id',$id);
        $this->db->where('p.status =',1);
        if(!empty($_GET['category'])) {
            $this->db->where('p.category_id',$_GET['category']);
        }
        if(!empty($_GET['product'])){
            $this->db->like('title',$_GET['product']);
        }
        if(isset($_GET['product_category']) && $_GET['product_category'] !=0) {
            $this->db->where('category_id',$_GET['product_category']);
        }
        if(!empty($_GET['product_price'])){
            $this->db->order_by('p.price',$_GET['product_price']);
        }
        if ($total == 1) {
            $query = $this->db->get();
            $query = $query->num_rows();  
        } else {
            $query = $this->db->get('', $limit, $offset);
            $query = $query->result();
            foreach ($query as $key => $value) {
                $this->db->select();
                $this->db->from('product_images');
                $this->db->where('product_id',$value->id);
                $this->db->limit(1);
                $query2 = $this->db->get();
                $query2 = $query2->result();
                $query[$key]->images = $query2;
            }  
        }
        return $query;
    }


    function get_services($id, $total, $limit, $offset)
    {
        $this->db->select('s.*,c.name as category_name');
        $this->db->from('services as s');
        if(!empty($_GET['category'])) {
            $this->db->where('category_id',$_GET['category']);
        }
        $this->db->where('s.business_id',$id);
        $this->db->join('categories as c','s.category_id=c.id','LEFT');
        $this->db->where('s.status',1);
        if ($total == 1) {
            $query = $this->db->get();
            $query = $query->num_rows();  
        } else {
            $query = $this->db->get('', $limit, $offset);
            $query = $query->result();  
        }
        return $query;
    }


    function get_product_img($id,$limit)
    {
        $this->db->select();
        $this->db->from('product_images');
        $this->db->where('product_id', $id);
        $this->db->limit($limit);
        $this->db->order_by('id','ASC');
        $query = $this->db->get();
        $query = $query->result();  
        return $query;
    }


    function get_single_product_img($id,$limit)
    {
        $this->db->select();
        $this->db->from('product_images');
        $this->db->where('product_id', $id);
        $this->db->limit($limit);
        $this->db->order_by('id','ASC');
        $query = $this->db->get();
        $query = $query->row();  
        return $query;
    }


    function get_related_products($id,$business_id, $limit)
    {
        $this->db->select('p.*');
        $this->db->from('products as p');
        $this->db->where('p.id !=', $id);
        $this->db->where('p.business_id',$business_id);
        $this->db->where('p.status',1);
        $this->db->limit($limit);
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get();
        $query = $query->result();
        foreach ($query as $key => $value) {
                $this->db->select();
                $this->db->from('product_images');
                $this->db->where('product_id',$value->id);
                $this->db->limit(1);
                $query2 = $this->db->get();
                $query2 = $query2->result();
                $query[$key]->images = $query2;
            }    
        return $query;
    }


    
    function check_customer_email($email)
    {
        $this->db->select();
        $this->db->from('users');
        $this->db->where('email', $email);
        $query = $this->db->get();
        $query = $query->row();  
        return $query;
    }


    
    function get_price_event($id)
    {
        $this->db->select();
        $this->db->from('events');
        $this->db->where('id', $id);
        $query = $this->db->get();
        $query = $query->row();  
        return $query;
    }



    function get_customer_orders()
    {
        $this->db->select();
        $this->db->from('product_orders'); 
        $this->db->where('customer_id', $this->session->userdata('id'));
        $this->db->order_by('id','DESC');
        $query = $this->db->get();
        $query = $query->result();  
        return $query;  
    }

    function get_order($id)
    {
        $this->db->select();
        $this->db->from('product_order_lists');
        $this->db->where('order_id',$id);
        $query = $this->db->get();
        $query = $query->result();
        return $query;
    }

    function get_event($id)
    {
        $this->db->select();
        $this->db->from('event_booking');
        $this->db->where('id',$id);
        $query = $this->db->get();
        $query = $query->result();
        return $query;
    }
    function get_skills()
    {
        $this->db->select();
        $this->db->from('skills');
        $this->db->where('status',1);
        $query = $this->db->get();
        $query = $query->result();
        return $query;
    }

    function get_all_mentors($total, $limit, $offset)
    {
        $this->db->select('u.*, us.skill_id, c.slug as category_slug');
        $this->db->from('users u');
        $this->db->where('u.role', 'user');
        $this->db->where('u.status', 1);
        $this->db->where('u.visible_profile', 1);
        $this->db->join('users_skill as us','u.id = us.user_id','LEFT');
        $this->db->join('categories as c','c.id = u.category','LEFT');
            
        if(!empty($_GET['mentor_search_name'])){
            $this->db->like('u.name', $_GET['mentor_search_name']);
        }

        if(!empty($_GET['mentor_search_country'])){
            $this->db->where('u.country', $_GET['mentor_search_country']);
        }

        if(!empty($_GET['mentor_search_experience'])){
            $this->db->where('u.experience_year', $_GET['mentor_search_experience']);
        }

        if(!empty($_GET['mentor_search_skill'])){
            $this->db->where('us.skill_id', $_GET['mentor_search_skill']);
        }

        if(!empty($_GET['category'])){
            $this->db->where('u.category', $_GET['category']);
        }
            
        if(!empty($_GET['search_name'])){
            $this->db->group_start();
            $this->db->like('u.name', $_GET['search_name']);
            $this->db->or_like('u.language', $_GET['search_name']);
            $this->db->group_end();
        }


        if(!empty($_GET['search_category'])){
            $this->db->where('u.category', $_GET['search_category']);
        }

        if(!empty($_GET['search_country'])){
            $this->db->where('u.country', $_GET['search_country']);
        }
        $this->db->group_by('u.id');
        $this->db->order_by('u.name','ASC');

        if ($total == 1) {
            $query = $this->db->get();
            $query = $query->num_rows();
            return $query;
        } else {
            $query = $this->db->get('', $limit, $offset);
            $query = $query->result();
            return $query;
        }
    }



    function get_mentors()
    {
        $this->db->select('u.*, us.skill_id, c.slug as category_slug');
        $this->db->from('users u');
        $this->db->where('u.role', 'user');
        $this->db->where('u.status', 1);
        $this->db->where('u.visible_profile', 1);
        $this->db->join('users_skill as us','u.id = us.user_id','LEFT');
        $this->db->join('categories as c','c.id = u.category','LEFT');
            
        if(!empty($_GET['mentor_search_name'])){
            $this->db->like('u.name', $_GET['mentor_search_name']);
        }

        if(!empty($_GET['mentor_search_country'])){
            $this->db->where('u.country', $_GET['mentor_search_country']);
        }

        if(!empty($_GET['mentor_search_experience'])){
            $this->db->where('u.experience_year', $_GET['mentor_search_experience']);
        }

        if(!empty($_GET['mentor_search_skill'])){
            $this->db->where('us.skill_id', $_GET['mentor_search_skill']);
        }

        if(!empty($_GET['category'])){
            $this->db->where('u.category', $_GET['category']);
        }
            
        if(!empty($_GET['search_name'])){
            $this->db->group_start();
            $this->db->like('u.name', $_GET['search_name']);
            $this->db->or_like('u.language', $_GET['search_name']);
            $this->db->group_end();
        }


        if(!empty($_GET['search_category'])){
            $this->db->where('u.category', $_GET['search_category']);
        }

        if(!empty($_GET['search_country'])){
            $this->db->where('u.country', $_GET['search_country']);
        }
        $this->db->group_by('u.id');
        $query = $this->db->get();
        $query = $query->result();
        return $query;
    }

    function get_home_mentors()
    {
        $this->db->select('u.*');
        $this->db->from('users u');
        $this->db->where('u.role', 'user');
            
        if(!empty($_GET['home_search_name'])){
            $this->db->like('u.name', $_GET['home_search_name']);
            $this->db->or_like('u.language', $_GET['home_search_name']);
        }

        if(!empty($_GET['home_search_category'])){
            $this->db->where('u.category', $_GET['home_search_category']);
        }

        if(!empty($_GET['home_search_country'])){
            $this->db->where('u.country', $_GET['home_search_country']);
        }

        $query = $this->db->get();
        $query = $query->result();
        return $query;
    }

    function get_slide_mentors()
    {
        $this->db->select();
        $this->db->from('users');
        $this->db->where('role', 'user');
        $this->db->where('status', 1);
        $this->db->where('visible_profile', 1);
        $this->db->limit(10);
        $query = $this->db->get();
        $query = $query->result();
        return $query;
    }

    function get_random_mentors()
    {
        $this->db->select('u.image');
        $this->db->from('users as u');
        $this->db->where('u.role', 'user');
        $this->db->where('u.status', 1);
        $this->db->order_by('RAND()');
        $this->db->limit(5);
        $query = $this->db->get();
        $query = $query->result_array();
        return $query;
    }

    function count_mentor_by_category($category_id)
    {
        $this->db->select();
        $this->db->from('users');
        $this->db->where('role', 'user');
        $this->db->where('status', 1);
        $this->db->where('category', $category_id);
        $query = $this->db->get();
        $query = $query->num_rows();
        if (empty($query)) {
            return '0';
        }else{
            return $query;
        }
    }

    function get_home_mentor_search($key)
    {
        $this->db->select();
        $this->db->from('users');
        $this->db->like('name', $key);
        $query = $this->db->get();
        $query = $query->result();
        return $query;
    }

    function get_user_skills($mentor_id)
    {
        $this->db->select();
        $this->db->from('users_skill');
        $this->db->where('user_id', $mentor_id);
        //$this->db->limit(2);
        $this->db->order_by('id','ASC');
        $query = $this->db->get();
        $query = $query->result_array();
        return $query;
    }

    function get_mentor_skills($mentor_id)
    {
        $this->db->select();
        $this->db->from('users_skill');
        $this->db->where('user_id', $mentor_id);
        //$this->db->limit(2);
        $this->db->order_by('id','ASC');
        $query = $this->db->get();
        $query = $query->result();
        return $query;
    }





    function get_user_sessions($mentor_id)
    {
        $this->db->select();
        $this->db->from('sessions as s');
        $this->db->where('user_id', $mentor_id);
        $this->db->where('status', 1);
        //$this->db->limit(2);
        $this->db->order_by('id','ASC');
        $query = $this->db->get();
        $query = $query->result();
        return $query;
    }

    function get_all_mentees()
    {
        $this->db->select();
        $this->db->from('users');
        $this->db->where('role', 'mentee');
        $this->db->order_by('id','ASC');
        $query = $this->db->get();
        $query = $query->result();
        return $query;
    }

    function get_all_user_mentees($id)
    {
        $this->db->select('s.*, u.name as mentee_name, u.id as mentee_id');
        $this->db->from('session_booking as s');
        $this->db->where('s.user_id', $id);
        $this->db->group_by('s.mentee_id');
        $this->db->join('users as u','u.id = s.mentee_id','LEFT');
        $this->db->order_by('id','ASC');
        $query = $this->db->get();
        $query = $query->result();
        return $query;
    }

    function get_all_mentee_mentors($id)
    {
        $this->db->select('s.*, u.name as mentor_name, u.role');
        $this->db->from('session_booking as s');
        $this->db->where('s.mentee_id', $id);
        $this->db->where('u.role', 'user');
        $this->db->group_by('s.user_id');
        $this->db->join('users as u','u.id = s.user_id','LEFT');
        $this->db->order_by('id','ASC');
        $query = $this->db->get();
        $query = $query->result();
        return $query;
    }

    function get_mentor_by_slug($slug)
    {
        $this->db->select();
        $this->db->from('users');
        $this->db->where('role', 'user');
        $this->db->where('slug', $slug);
        $query = $this->db->get();
        $query = $query->row();
        return $query;
    }

    function get_single_mentor($id)
    {
        $this->db->select();
        $this->db->from('users');
        $this->db->where('role', 'user');
        $this->db->where('id', $id);
        $query = $this->db->get();
        $query = $query->row();
        return $query;
    }


    function check_favourite($favourite_id, $user_id)
    {
        $this->db->select();
        $this->db->from('favourite');
        $this->db->where('user_id', $user_id);
        $this->db->where('favourite_id', $favourite_id);
        $query = $this->db->get();
        $query = $query->row();
        return $query;
    }

    function check_mentor_slug($slug)
    {
        $this->db->select();
        $this->db->from('users');
        $this->db->where('slug', $slug);
        $query = $this->db->get();
        $query = $query->row();
        if (empty($query)) {
            return 0;
        }else{
            return 1; 
        }
        
    }

    function get_single_session($slug)
    {
        $this->db->select();
        $this->db->from('sessions');
        $this->db->where('slug', $slug);
        $query = $this->db->get();
        $query = $query->row();
        return $query;
    }

    function get_single_session_by_id($slug)
    {
        $this->db->select();
        $this->db->from('sessions');
        $this->db->where('id', $slug);
        $query = $this->db->get();
        $query = $query->row();
        return $query;
    }

    function get_booked_session($uid)
    {
        $this->db->select();
        $this->db->from('sessions');
        $this->db->where('uid', $uid);
        $query = $this->db->get();
        $query = $query->row();
        return $query;
    }

    function check_recurring_session($id)
    {
        $this->db->select();
        $this->db->from('session_booking');
        $this->db->where('session_id', $id);
        $this->db->where('is_recurring', 1);
        $query = $this->db->get();
        $query = $query->row();
        return $query;
    }


    function get_my_session_days($session_id)
    {
        $this->db->select();
        $this->db->from('assaign_days');
        $this->db->where('session_id', $session_id);
        $query = $this->db->get();
        $query = $query->result_array();  
        return $query;
    }
    function get_my_session_days_default($mentor_id)
    {
        $this->db->select();
        $this->db->from('assaign_days');
        $this->db->where('session_id', 0);
        $this->db->where('user_id', $mentor_id);
        $query = $this->db->get();
        $query = $query->result_array();  
        return $query;
    }

    function get_timeslot_by_day($day_id, $session_id)
    {
        $this->db->select();
        $this->db->from('assign_time');
        $this->db->where('day_id', $day_id);
        $this->db->where('session_id', $session_id);
        $query = $this->db->get();
        $query = $query->result_array();  
        return $query;
    }

    function get_timeslot_by_day_default($day_id, $mentor_id)
    {
        $this->db->select();
        $this->db->from('assign_time');
        $this->db->where('day_id', $day_id);
        $this->db->where('session_id', 0);
        $this->db->where('user_id', $mentor_id);
        $query = $this->db->get();
        $query = $query->result_array();  
        return $query;
    }

    function get_time_by_days($day_id, $session_id)
    {
        $this->db->select();
        $this->db->from('assign_time');
        $this->db->where('day_id', $day_id);
        $this->db->where('session_id', $session_id);
        $this->db->group_by('time');
        $query = $this->db->get();
        $query = $query->result();  
        return $query;
    }

    function get_time_by_default_days($day_id, $user_id)
    {
        $this->db->select();
        $this->db->from('assign_time');
        $this->db->where('day_id', $day_id);
        $this->db->where('user_id', $user_id);
        $this->db->group_by('time');
        $query = $this->db->get();
        $query = $query->result();  
        return $query;
    }





    function get_mentor_experiences($id)
    {
        $this->db->select();
        $this->db->from('experiences');
        $this->db->where('user_id', $id);
        $this->db->where('status', 1);
        $query = $this->db->get();
        $query = $query->result();
        return $query;
    }



    function get_mentor_educations($id)
    {
        $this->db->select();
        $this->db->from('educations');
        $this->db->where('user_id', $id);
        $this->db->where('status', 1);
        $query = $this->db->get();
        $query = $query->result();
        return $query;
    }




    function get_customer_careers()
    {
        $this->db->select();
        $this->db->from('product_orders'); 
        $this->db->where('customer_id', $this->session->userdata('id'));
        $this->db->order_by('order_date','DESC');
        $query = $this->db->get();
        $query = $query->result();  
        return $query;  
    }


    function get_customer_events()
    {
        $this->db->select();
        $this->db->from('event_booking'); 
        $this->db->where('customer_id', $this->session->userdata('id'));
        $this->db->order_by('booking_date','DESC');
        $query = $this->db->get();
        $query = $query->result();  
        return $query;  
    }


    function get_customer_appointments()
    {
        $this->db->select();
        $this->db->from('appointments'); 
        $this->db->where('customer_id', $this->session->userdata('id'));
        $this->db->order_by('date','DESC');
        $query = $this->db->get();
        $query = $query->result();  
        return $query;  
    }



    function get_events($id, $total, $limit, $offset)
    {
        $this->db->select('e.*,c.name as category_name');
        $this->db->from('events as e');
        $this->db->join('categories as c','e.category_id=c.id','LEFT');
        $this->db->where('e.business_id', $id);
        $this->db->where('e.status =',1);
        if(!empty($_GET['category'])) {
            $this->db->where('e.category_id',$_GET['category']);
        }
        if(!empty($_GET['event'])){
            $this->db->like('e.title',$_GET['event']);
        }
        if(!empty($_GET['event_category'])) {
            $this->db->where('e.category_id',$_GET['event_category']);
        }
        if(!empty($_GET['event_price'])){
            $this->db->order_by('e.price',$_GET['event_price']);
        }
        if ($total == 1) {
            $query = $this->db->get();
            $query = $query->num_rows();  
        } else {
            $query = $this->db->get('', $limit, $offset);
            $query = $query->result();  
        }
        return $query;
    }


    function get_home_blogs($id,$limit)
    {
        $this->db->select('b.*,c.name as category_name');
        $this->db->from('blogs as b');
        $this->db->join('categories as c','b.category_id=c.id','LEFT');
        $this->db->where('b.business_id', $id);
        $this->db->where('b.status',1);
        $this->db->limit($limit);
        $query = $this->db->get();
        $query = $query->result();  
        return $query;
    }


    function get_new_services($id)
    {
        $this->db->select('s.*,c.name as category_name');
        $this->db->from('services as s');
        $this->db->join('categories as c','s.category_id=c.id','LEFT');
        $this->db->where('s.business_id', $id);
        $this->db->where('s.status =',1);
        if(!empty($_GET['category'])) {
            $this->db->where('s.category_id',$_GET['category']);
        }
        $query = $this->db->get();
        $query = $query->result();
        return $query;
    }


    function get_home_services($id,$limit)
    {
        $this->db->select('s.*,c.name as category_name');
        $this->db->from('services as s');
        $this->db->join('categories as c','s.category_id=c.id','LEFT');
        $this->db->where('s.business_id', $id);
        $this->db->where('s.status =',1);
        $this->db->limit($limit);
        $query = $this->db->get();
        $query = $query->result();  
        return $query;
    }


    function get_home_counters($id,$limit)
    {
        $this->db->select('*');
        $this->db->from('counters');
        $this->db->where('business_id', $id);
        $this->db->where('status =',1);
        $this->db->limit($limit);
        $query = $this->db->get();
        $query = $query->result();  
        return $query;
    }


    function get_home_testimonials($id,$limit)
    {
        $this->db->select('*');
        $this->db->from('testimonials');
        $this->db->where('business_id', $id);
        $this->db->limit($limit);
        $query = $this->db->get();
        $query = $query->result();  
        return $query;
    }


    function get_home_sliders($id,$limit)
    {
        $this->db->select('*');
        $this->db->from('sliders');
        $this->db->where('business_id', $id);
        $this->db->limit($limit);
        $query = $this->db->get();
        $query = $query->result();  
        return $query;
    }


    function get_home_products($business_id, $limit)
    {
        $this->db->select('p.*');
        $this->db->from('products as p');
        $this->db->where('p.business_id',$business_id);
        $this->db->where('p.status',1);
        $this->db->limit($limit);
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get();
        $query = $query->result();
        foreach ($query as $key => $value) {
                $this->db->select();
                $this->db->from('product_images');
                $this->db->where('product_id',$value->id);
                $this->db->limit(1);
                $query2 = $this->db->get();
                $query2 = $query2->result();
                $query[$key]->images = $query2;
            }    
        return $query;
    }


    function get_home_faqs($id)
    {
        $this->db->select('*');
        $this->db->from('faqs');
        $this->db->where('business_id', $id);
        $query = $this->db->get();
        $query = $query->result();  
        return $query;
    }


    function get_about_us_images($id,$limit)
    {
        $this->db->select('*');
        $this->db->from('about_images');
        $this->db->where('business_id', $id);
        $this->db->limit($limit);
        $query = $this->db->get();
        $query = $query->result();  
        return $query;
    }



    function get_portfolios($id, $total, $limit, $offset)
    {
        $this->db->select('p.*,c.name as category_name, c.slug as category_slug');
        $this->db->from('portfolios as p');
        $this->db->join('categories as c','p.category_id=c.id','LEFT');
        $this->db->where('p.business_id', $id);
        $this->db->where('p.status =',1);
        if(!empty($_GET['category'])) {
            $this->db->where('p.category_id',$_GET['category']);
        }
        if ($total == 1) {
            $query = $this->db->get();
            $query = $query->num_rows();  
        } else {
            $query = $this->db->get('', $limit, $offset);
            $query = $query->result();  
        }
        return $query;
    }



    function get_careers($id, $total, $limit, $offset)
    {
        $this->db->select('j.*, c.name as category_name');
        $this->db->from('jobs as j');
        $this->db->join('categories as c','j.category_id=c.id','LEFT');
        $this->db->where('j.business_id', $id);
        $this->db->where('j.status =',1);
        if(!empty($_GET['career'])){
            $this->db->like('j.title',$_GET['career']);
        }
        if(!empty($_GET['career_category'])) {
            $this->db->where('j.category_id',$_GET['career_category']);
        }
        if(!empty($_GET['career_type'])) {
            $this->db->where('j.employment_status',$_GET['career_type']);
        }
        if ($total == 1) {
            $query = $this->db->get();
            $query = $query->num_rows();  
        } else {
            $query = $this->db->get('', $limit, $offset);
            $query = $query->result();  
        }
        return $query;
    }



    function get_home_portfolios($id, $limit)
    {
        $this->db->select('p.*,c.name as category_name');
        $this->db->from('portfolios as p');
        $this->db->join('categories as c','p.category_id=c.id','LEFT');
        $this->db->where('p.business_id', $id);
        $this->db->where('p.status',1);
        $this->db->limit($limit);
        $query = $this->db->get();
        $query = $query->result();
        return $query;
    }


    function get_related_data($id, $table)
    {
        $this->db->select();
        $this->db->from($table);
        $this->db->where('id !=', $id);
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get();
        $query = $query->result();  
        return $query;
    }


    function get_comments($id)
    {
        $this->db->select();
        $this->db->from('comments');
        $this->db->where('post_id', $id);
        //$this->db->order_by('id', 'DESC');
        $query = $this->db->get();
        $query = $query->result();  
        return $query;
    }


    function get_faqs($id)
    {
        $this->db->select();
        $this->db->from('faqs');
        if ($id == 0) {
            $this->db->where('business_id', '');
        }else{
            $this->db->where('business_id', $id);
        }
        $query = $this->db->get();
        $query = $query->result();  
        return $query;
    }


    function get_related_blogs($id,$business_id, $limit)
    {
        $this->db->select('b.*,c.name as category_name');
        $this->db->from('blogs as b');
        $this->db->where('b.business_id', $business_id);
        $this->db->join('categories as c','b.category_id=c.id','LEFT');
        $this->db->where('b.id !=', $id);
        $this->db->where('b.status', 1);
        $this->db->limit($limit);
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get();
        $query = $query->result();  
        return $query;
    }


    function get_related_portfolios($id,$business_id,$limit)
    {
        $this->db->select('p.*,c.name as category_name');
        $this->db->from('portfolios as p');
        $this->db->join('categories as c','p.category_id=c.id','LEFT');
        $this->db->where('p.business_id',$business_id);
        $this->db->where('p.id !=', $id);
        $this->db->where('p.status', 1);
        $this->db->limit($limit);
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get();
        $query = $query->result();  
        return $query;
    }



    function get_popular_blogs($id,$limit)
    {
        $this->db->select('*');
        $this->db->from('blogs');
        //$this->db->join('categories as c','b.category_id=c.id','LEFT');
        $this->db->limit($limit);
        $this->db->where('business_id',$id);
        $this->db->order_by('total_views', 'DESC');
        $query = $this->db->get();
        $query = $query->result();  
        return $query;
    }           

    // select by slug
    function get_by_status($business_id, $table)
    {
        $this->db->select();
        $this->db->from($table);
        $this->db->where('business_id', $business_id);
        $this->db->where('status', 1);
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get();
        $query = $query->result();  
        return $query;
    } 

    // select by status
    function select_by_status($table)
    {
        $this->db->select();
        $this->db->from($table);
        $this->db->where('status', 1);
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get();
        $query = $query->result();  
        return $query;
    } 

    // get locations
    function get_locations($type, $business_id){
        $this->db->select();
        $this->db->from('locations');
        $this->db->where('business_id', $business_id);
        $this->db->where('status', 1);
        if ($type == 0) {
            $this->db->where('parent_id', 0);
        }else{
            $this->db->where('parent_id !=', 0);
        }
        $query = $this->db->get();
        $query = $query->result();  
        return $query;
    } 

    // get locations
    function get_sub_locations($location_id){
        $this->db->select();
        $this->db->from('locations');
        $this->db->where('status', 1);
        $this->db->where('parent_id', $location_id);
        $query = $this->db->get();
        $query = $query->result();  
        return $query;
    } 

    // get coupon
    function get_coupon($code, $service_id, $business_id)
    {
        $this->db->select();
        $this->db->from('coupons');
        $this->db->where('code', $code);
        $this->db->where('service_id', $service_id);
        $this->db->where('business_id', $business_id);
        $this->db->where('status', 1);
        $query = $this->db->get();
        $query = $query->row();  
        return $query;
    } 

    // get coupon apply
    function check_coupon_apply($code, $service_id, $business_id, $customer_id)
    {
        $this->db->select();
        $this->db->from('coupons_apply');
        $this->db->where('code', $code);
        $this->db->where('service_id', $service_id);
        $this->db->where('business_id', $business_id);
        $this->db->where('customer_id', $customer_id);
        $query = $this->db->get();
        $query = $query->row();  
        return $query;
    } 


    // check coupon
    function check_service_coupon($service_id, $business_id)
    {
        $this->db->select();
        $this->db->from('coupons');
        $this->db->where('service_id', $service_id);
        $this->db->where('business_id', $business_id);
        $this->db->where('status', 1);
        $this->db->where('usages_limit !=', 0);
        $query = $this->db->get();
        $query = $query->row();  
        return $query;
    } 


    // check coupon apply
    function check_customer_coupon($appointment_id)
    {
        $this->db->select();
        $this->db->from('coupons_apply');
        $this->db->where('appointment_id', $appointment_id);
        $query = $this->db->get();
        $query = $query->row();  
        return $query;
    } 


    // select by status
    function get_random_staffs($business_id, $service_id, $table)
    {
        $this->db->select();
        $this->db->from($table);
        $this->db->where('business_id', $business_id);
        $this->db->where('id', $service_id);
        $this->db->where('status', 1);
        $this->db->order_by('id', 'RANDOM');
        $query = $this->db->get();
        $query = $query->row();  
        return $query;
    } 



    function get_category_services($business_id)
    {
        $this->db->select('*');
        $this->db->from('service_category');
        $this->db->where('status', 1);
        $this->db->where('business_id', $business_id);
        $this->db->order_by('orders', 'ASC');
        $query = $this->db->get();
        $query = $query->result();  

        foreach ($query as $key => $value) {
            $this->db->select('s.*');
            $this->db->from('services s');
            $this->db->where('s.category_id',$value->id);
            $this->db->where('s.business_id', $business_id);
            $query2 = $this->db->get();
            $query2 = $query2->result();
            $query[$key]->services = $query2;
        }
        return $query;
    }


    // select by status
    function get_service_staffs($service_id)
    {
        $this->db->select('s.*');
        $this->db->from('services as s');
        $this->db->where('id', $service_id);
        $this->db->where('status', 1);
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get();
        $query = $query->row();  
        return $query;
    } 


    // select by status
    function get_service_staffs__($service_id)
    {
        $this->db->select('*');
        $this->db->from('staffs');
        $this->db->like('service_id', json_decode($service_id));
        $this->db->where('status', 1);
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get();
        $query = $query->result();  
        return $query;
    } 


    // select by slug
    function get_company_services($business_id, $limit)
    {
        $this->db->select();
        $this->db->from('services');
        $this->db->where('business_id', $business_id);
        if ($limit != 0) {
            $this->db->limit($limit);
        }

        if (!empty($_GET['search'])) {
            $this->db->like('name', $_GET['search']);
        }

        if (!empty($_GET['staffs'])) {
            //print_r(json_encode($_GET['staffs']));
            $this->db->like('staffs', json_decode($_GET['staffs']));
        }


        $this->db->order_by('id', 'DESC');
        $query = $this->db->get();
        $query = $query->result();  
        return $query;
    } 


    function check_customer($user_name)
    {
        $this->db->select();
        $this->db->from('customers');
        $this->db->where('phone', '+'.$user_name);
        $this->db->or_where('email', $user_name);
        $query = $this->db->get();
        $query = $query->row();  
        return $query;
    } 

    public function check_email($email)
    {
        $this->db->select('*');
        $this->db->from('users');
        $this->db->where('email', $email); 
        $this->db->limit(1);
        $query = $this->db->get();
        if($query->num_rows() == 1) {                 
            return $query->result();
        }else{
            return false;
        }
    }

    public function check_username($name)
    {
        $this->db->select('*');
        $this->db->from('users');
        $this->db->where('user_name', $name); 
        $this->db->limit(1);
        $query = $this->db->get();
        if($query->num_rows() == 1) {                 
            return $query->result();
        }else{
            return 0;
        }
    }


    // select function
    function get_single_page($slug)
    {
        $this->db->select();
        $this->db->from('pages');
        $this->db->where('slug', $slug);
        $query = $this->db->get();
        $query = $query->row();  
        return $query;
    }


    //get category
    public function get_category($id)
    {
        $this->db->where('id', $id);
        $query = $this->db->get('category');
        return $query->row();
    }

    //get category
    public function get_category_option($id, $table)
    {
        $this->db->where('id', $id);
        $query = $this->db->get($table);
        return $query->row();
    }


    function get_subcategory()
    {
        $this->db->select();
        $this->db->from('category');
        $this->db->where('status', 1);
        $query = $this->db->get();
        $query = $query->result_array();  
        return $query;
    }

    // get settings
    function get_settings()
    {
        $this->db->select('s.*, l.short_name, l.name as language_name, l.slug as lang_slug, l.text_direction as dir');
        $this->db->from('settings s');
        $this->db->join('language as l', 'l.id = s.lang', 'LEFT');
        $query = $this->db->get();
        $query = $query->row();  
        return $query;
    }


    function get_slug_by_language($slug,$table)
    {
        $this->db->select();
        $this->db->from($table);
        $this->db->where('slug', $slug);
        $query = $this->db->get();
        $query = $query->row();  
        return $query;
    } 

    // select by id
    function select_option_md5($id,$table)
    {
        $this->db->select();
        $this->db->from($table);
        $this->db->where(md5('id'), $id);
        $query = $this->db->get();
        $query = $query->row();  
        return $query;
    } 

    //get user by id
    public function get_user_by_slug($slug)
    {
        $this->db->select('u.*');
        $this->db->from('users u');
        $this->db->where('u.slug', $slug);
        $query = $this->db->get();
        $query = $query->row();  
        return $query;
    }

    // asc select function
    function get_customers_by_userid($user_id)
    {
        $this->db->select();
        $this->db->from('customers');
        $this->db->where('user_id', $user_id);
        $this->db->order_by('id','ASC');
        $query = $this->db->get();
        $query = $query->result();  
        return $query;
    }

    // get pricing packages
    function get_pricing()
    {
        $this->db->select('*');
        $this->db->from('package');
        $query = $this->db->get();
        $query = $query->result_array();  

        foreach ($query as $key => $value) {
            $this->db->select('*');
            $this->db->from('features f');
            $this->db->where('f.package_id',$value['id']);
            $query2 = $this->db->get();
            $query2 = $query2->result_array();
            $query[$key]['features'] = $query2;
        }
        return $query;
    }


    // get site blog posts
    function get_home_blog_posts(){
        $this->db->select('b.*');
        $this->db->select('c.slug as category_slug, c.name as category, u.role');
        $this->db->from('blogs b');
        $this->db->where('b.status', 1);
        $this->db->where('b.business_id', '');
        $this->db->join('categories c', 'c.id = b.category_id', 'LEFT');
        $this->db->join('users u', 'u.id = b.user_id', 'LEFT');
        $this->db->limit(3);
        $this->db->group_by('b.id');
        $this->db->order_by('b.id', 'DESC');
        $query = $this->db->get();
        $query = $query->result();
        return $query;
    } 


    // get all users
    function get_home_users($total, $limit, $offset)
    {
        $this->db->select();
        $this->db->from('users');
        $this->db->where('role', 'user');

        if (!empty($_GET['search'])) {
            $this->db->like('name', $_GET['search']);
        }
        $this->db->order_by('id','DESC');
        if ($total == 1) {
            $query = $this->db->get();
            $query = $query->num_rows();
            return $query;
        } else {
            $query = $this->db->get('', $limit, $offset);
            $query = $query->result();
            return $query;
        }
    }


    // get all users
    function get_total_user_by_type($type)
    {
        $this->db->select();
        $this->db->from('users');
        $this->db->where('role', 'user');
        $this->db->where('account_type', $type);
        $query = $this->db->get();
        $query = $query->num_rows();
        return $query;
    }


    // get features
    function gets_home_features()
    {
        gets_active_langs();
        $this->db->select();
        $this->db->from('features');
        $query = $this->db->get();
        $query = $query->row();  
        return $query;
    }

    // get features
    function get_count($table)
    {   
        $this->db->select();
        $this->db->from($table);
        $query = $this->db->get();
        $query = $query->num_rows();  
        return $query;
    }


    // get features
    function get_feature_limit($id)
    {   
        $this->db->select();
        $this->db->from('features');
        $this->db->where('id', $id);
        $query = $this->db->get();
        $query = $query->row();  
        return $query;
    }


    // get features
    function get_feature($slug)
    {   
        $this->db->select();
        $this->db->from('features');
        $this->db->where('slug', $slug);
        $query = $this->db->get();
        $query = $query->row();  
        return $query;
    }


    // get_payment
    function get_payment($puid)
    {
        $this->db->select();
        $this->db->from('payment');
        $this->db->where('puid', $puid);
        $query = $this->db->get();
        $query = $query->row();  
        return $query;
    }


    // get_payment
    function get_package_by_slug($slug)
    {
        $this->db->select();
        $this->db->from('package');
        $this->db->where('slug', $slug);
        $query = $this->db->get();
        $query = $query->row();  
        return $query;
    }


    // select function
    function get_user_payment($user_id)
    {
        $this->db->select();
        $this->db->from('payment');
        $this->db->where('user_id', $user_id);
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get();
        $query = $query->row();  
        return $query;
    }



    // get
    function get_my_package()
    {
        if ($this->session->userdata('role') == 'user') {
            $user_id = $this->session->userdata('id');
        } else {
            $user_id = $this->session->userdata('parent');
        }
        
        $this->db->select('p.*, k.name as package_name, k.slug, k.id as package_id');
        $this->db->from('payment p');
        $this->db->join('package k', 'k.id = p.package_id', 'LEFT');
        $this->db->where('p.user_id', $user_id);
        $this->db->order_by('p.id', 'DESC');
        $query = $this->db->get();
        $query = $query->row();  
        return $query;
    }


    // get_payment
    function get_user_package($user_id)
    {

        $this->db->select('p.*, k.name as package_name, k.slug, k.id as package_id');
        $this->db->from('payment p');
        $this->db->join('package k', 'k.id = p.package_id', 'LEFT');
        $this->db->where('p.user_id', $user_id);
        $this->db->order_by('p.id', 'DESC');
        $query = $this->db->get();
        $query = $query->row();  
        return $query;
    }


    public function expire_payments()
    {   
        $payments = $this->get_expire_payments();
        foreach ($payments as $payment) {
            $data = array(
                'status' => 'expire',
                'expire_on' => '1970-01-01',
                'created_at' => '1970-01-01 01:01:01'
            );
            $data = $this->security->xss_clean($data);
            $this->common_model->update($data, $payment->id, 'payment');
        }
    }

    // get expire payments
    function get_expire_payments(){
        $this->db->select();
        $this->db->from('payment');
        $this->db->where('expire_on', date('Y-m-d'));
        $this->db->or_where('expire_on <', date('Y-m-d'));
        $query = $this->db->get();
        $query = $query->result();  
        return $query;
    } 

    // get trial users
    function get_trial_users(){
        $this->db->select();
        $this->db->from('users');
        $this->db->where('trial_expire', date('Y-m-d'));
        $this->db->or_where('trial_expire <', date('Y-m-d'));
        $query = $this->db->get();
        $query = $query->result();  
        return $query;
    } 

    
    // get testimonials
    function get_testimonials(){
        $this->db->select();
        $this->db->from('testimonials');
        $this->db->where('type', 'admin');
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get();
        $query = $query->result();  
        return $query;
    } 

    // get categories
    function get_categories(){
        $this->db->select();
        $this->db->from('categories');
        //$this->db->where('parent_id', 0);
        $this->db->where('status', 1);
        $this->db->order_by('id', 'ASC');
        $query = $this->db->get();
        $query = $query->result();  
        return $query;
    }

    function get_site_brands(){
        $this->db->select();
        $this->db->from('brands');
        $this->db->where('user_id', 0);
        $this->db->where('status', 1);
        $this->db->order_by('id', 'ASC');
        $query = $this->db->get();
        $query = $query->result();  
        return $query;
    }

    function get_mentors_count(){
        $this->db->select();
        $this->db->from('users');
        $this->db->where('role', 'user');
        $this->db->where('status', 1);
        $this->db->order_by('id', 'ASC');
        $query = $this->db->get();
        $query = $query->num_rows();  
        return $query;
    }

    function get_countries_count(){
        $this->db->select();
        $this->db->from('users');
        $this->db->where('role !=', 'admin');
        $this->db->where('status', 1);
        $this->db->order_by('id', 'ASC');
        $this->db->group_by('country');
        $query = $this->db->get();
        $query = $query->num_rows();  
        return $query;
    }

    function get_bookings_count(){
        $this->db->select();
        $this->db->from('session_booking');
        $this->db->order_by('id', 'ASC');
        $query = $this->db->get();
        $query = $query->num_rows();  
        return $query;
    }    


    // get blog posts
    function get_blog_posts($total, $limit, $offset, $user_id){
        $this->db->select('b.*');
        $this->db->select('c.slug as category_slug, c.name as category');
        $this->db->from('blog_posts b');
        $this->db->where('b.user_id', $user_id);
        $this->db->join('blog_category c', 'c.id = b.category_id', 'RIGHT');
        $this->db->limit($limit);
        
        if ($total == 1) {
            $query = $this->db->get();
            $query = $query->num_rows();
            return $query;
        } else {
            $query = $this->db->get('', $limit, $offset);
            $query = $query->result();
            return $query;
        }
    } 



    function get_site_blog_posts($total, $limit, $offset){
        $this->db->select('b.*');
        $this->db->select('c.slug as category_slug, c.name as category, u.role');
        $this->db->from('blogs b');
        $this->db->where('u.role', 'admin');
        $this->db->where('b.status', 1);
        
        if(isset($_POST['search']) && $_POST['search'] != ''){
            $input = urlencode($_POST['search']);
            $this->db->like('b.title', $input);
        }
        
        $this->db->join('users u', 'u.id = b.user_id', 'LEFT');
        $this->db->join('categories c', 'c.id = b.category_id', 'LEFT');
        
        if ($total == 1) {
            $query = $this->db->get();
            $query = $query->num_rows();
            return $query;
        } else {
            $query = $this->db->get('', $limit, $offset);
            $query = $query->result();
            return $query;
        }
    } 


    //get posts categories
    function get_category_by_slug($slug)
    {
        $this->db->select();
        $this->db->from('blog_category');
        $this->db->where('slug', $slug);
        $query = $this->db->get();
        $query = $query->row();  
        return $query;
    }


    //get blog categories
    function get_blog_category($user_id)
    {
        $this->db->select();
        $this->db->from('blog_category');
        $this->db->where('user_id', $user_id);
        $query = $this->db->get();
        $query = $query->result();  
        return $query;
    }


    //get blog categories
    function get_post_details($slug)
    {
        $this->db->select('p.*, c.name as category_name');
        $this->db->from('blogs p');
        $this->db->join('categories c', 'p.category_id = c.id', 'LEFT');
        $this->db->where('p.slug', $slug);
        $query = $this->db->get();
        $query = $query->row();  
        return $query;
    }


    function get_mentor_post($slug)
    {
        $this->db->select();
        $this->db->from('blogs');
        $this->db->where('business_id', 1);
        $this->db->where('slug', $slug);
        $query = $this->db->get();
        $query = $query->row();  
        return $query;
    }

  


    //get latest posts
    function get_related_post($category_id, $post_id, $user_id)
    {
        $this->db->select('p.*');
        $this->db->select('c.name as category');
        $this->db->select('u.name as author_name');
        $this->db->from('blog_posts p');
        $this->db->join('blog_category c', 'c.id = p.category_id', 'LEFT');
        $this->db->join('users u', 'u.id = p.user_id', 'LEFT');
        $this->db->where('p.id !=', $post_id);
        $this->db->where('p.user_id', $user_id);
        $this->db->where('p.category_id', $category_id);
        $this->db->where('p.status', 1);
        $this->db->order_by('p.id', 'DESC');
        $this->db->limit(3);
        $query = $this->db->get();
        $query = $query->result();  
        return $query;
    }


    //get posts tags
    public function get_post_tags($post_id)
    {
        $this->db->where('post_id', $post_id);
        $query = $this->db->get('tags');
        return $query->result();
    }

    //get comments by img
    public function get_comments_by_post($post_id)
    {   
        $this->db->select('c.*');
        $this->db->from('comments c');
        $this->db->where('c.post_id', $post_id);
        $this->db->order_by('c.id', 'DESC');
        $query = $this->db->get();
        $query = $query->result();  
        return $query;
    }
   
    // delete tags
    function delete_tags($post_id, $table){
        $this->db->delete($table, array('post_id' => $post_id));
        return;
    }


    //get category posts
    function get_category_posts($total, $limit, $offset, $id, $user_id)
    {

        $this->db->select('p.*');
        $this->db->select('c.name as category, c.slug as category_slug');
        $this->db->from('blog_posts p');
        $this->db->join('blog_category as c', 'c.id = p.category_id', 'LEFT');
        $this->db->where('p.status', 1);
        $this->db->where('p.category_id', $id);
        $this->db->where('p.user_id', $user_id);
        
        $this->db->order_by('p.id', 'DESC');
        $this->db->limit($limit);
        
        if ($total == 1) {
            $query = $this->db->get();
            $query = $query->num_rows();
            return $query;
        } else {
            $query = $this->db->get('', $limit, $offset);
            $query = $query->result();
            return $query;
        }
    }


    //get category posts
    function count_posts_by_categories($id)
    {
        $this->db->select('count(p.id) as total');
        $this->db->from('blog_posts p');
        $this->db->where('p.status', 1);
        $this->db->where('p.category_id', $id);
        $query = $this->db->get();
        if($query->num_rows() == 1) {                 
            return $query->row();
        }else{
            return 0;
        }
    }

    //get random posts
    function get_random_tags($user_id)
    {
        $this->db->select('t.*');
        $this->db->select('p.status, p.slug as post_slug, u.name as author_name');
        $this->db->from('tags t');
        $this->db->join('blog_posts as p', 'p.id = t.post_id', 'LEFT');
        $this->db->join('users as u', 'u.id = p.user_id', 'LEFT');
        $this->db->where('p.status', 1);
        $this->db->where('p.user_id', $user_id);
        $this->db->order_by('rand()');
        $this->db->limit(8);
        $query = $this->db->get();
        $query = $query->result();  
        return $query;
    }


    // get_categories
    function get_blog_categories(){
        $this->db->select();
        $this->db->from('blog_category');
        $this->db->where('user_id', $this->session->userdata('id'));
        $query = $this->db->get();
        $query = $query->result();  
        return $query;
    } 

    //get latest users
    function get_latest_users(){
        $this->db->select('u.*');
        $this->db->from('users u');
        $this->db->where('u.status', 1);
        $this->db->order_by('u.id','DESC');
        $this->db->limit(6);
        $query = $this->db->get();
        $query = $query->result();
        return $query;
    }

    // get all posts
    function get_latest_messages(){
        $this->db->select('c.*');
        $this->db->from('contacts c');
        $this->db->order_by('c.id','DESC');
        $this->db->limit(8);
        $query = $this->db->get();
        $query = $query->result();
        return $query;
    }


    //get latest users
    function get_users(){
        $this->db->select('u.*');
        $this->db->from('users u');
        $this->db->where('u.status', 1);
        $this->db->where('u.role', 'user');
        $this->db->order_by('u.id','DESC');
        $query = $this->db->get();
        $query = $query->result();
        return $query;
    }



  


    //for single time reminder
    // function get_reminder_bookings($current_time, $reminder_time) {
    //     $this->db->select('*');
    //     $this->db->from('session_booking');
    //     $this->db->where('is_sent_reminder', 0);

    //     // reminder window: (X hours - 5 minutes) থেকে X hours
    //     $time_target = date("Y-m-d H:i", strtotime('+'.$reminder_time, strtotime($current_time)));
    //     $time_minus5 = date("Y-m-d H:i", strtotime('-5 minutes', strtotime($time_target)));

    //     $this->db->where('
    //         STR_TO_DATE(CONCAT(date, " ", SUBSTRING_INDEX(time, " - ", 1)), "%Y-%m-%d %H:%i")
    //         BETWEEN "'.$time_minus5.'" AND "'.$time_target.'"
    //     ', null, false);

    //     $query = $this->db->get();
    //     return $query->result();
    // }


    //for multiple time reminder
    function get_reminder_bookings($current_time, $reminder_times = []) {
        $this->db->select('*');
        $this->db->from('session_booking');
        $this->db->where('is_sent_reminder', 0);

        $this->db->group_start();

        foreach ($reminder_times as $reminder_time) {
            
            if (is_numeric($reminder_time)) {
                $reminder_time = $reminder_time . ' hour';
            }

            $time_target = date("Y-m-d H:i", strtotime('+'.$reminder_time, strtotime($current_time)));
            $time_minus5 = date("Y-m-d H:i", strtotime('-5 minutes', strtotime($time_target)));
            
            $this->db->or_where('
                STR_TO_DATE(CONCAT(date, " ", SUBSTRING_INDEX(time, " - ", 1)), "%Y-%m-%d %H:%i")
                BETWEEN "'.$time_minus5.'" AND "'.$time_target.'"
            ', null, false);
        }
        $this->db->group_end();

        $query = $this->db->get();
        return $query->result();
    }








    // get all users
    function get_all_users($total, $limit, $offset)
    {
        $this->db->select('u.*');
        $this->db->from('users as u');
        $this->db->where('role', 'user');

        if (isset($_GET['search']) && $_GET['search'] != '') {
            $this->db->like('u.name', $_GET['search']);
        }

        $this->db->order_by('u.id','DESC');
        $this->db->group_by('u.id');

        if ($total == 1) {
            $query = $this->db->get();
            $query = $query->num_rows();
            return $query;
        } else {
            $query = $this->db->get('', $limit, $offset);
            $query = $query->result();
            return $query;
        }
    }



    function get_all_business($total, $limit, $offset)
    {
        $this->db->select('b.*, n.name as country_name, n.currency_name, n.currency_symbol, n.currency_code, c.name as category_name');
        $this->db->from('business b');
       
        if (isset($_GET['search']) && $_GET['search'] != '') {
            $this->db->like('b.name', $_GET['search']);
        }

        if (isset($_GET['category']) && $_GET['category'] != '') {
            $this->db->like('b.category', $_GET['category']);
        }

        if (isset($_GET['country']) && $_GET['country'] != '') {
            $this->db->like('b.country', $_GET['country']);
        }
        
        $this->db->where('u.status', 1);
        $this->db->join('users u', 'u.id = b.user_id', 'LEFT');
        $this->db->join('country n', 'n.id = b.country', 'LEFT');
        $this->db->join('categories c', 'c.id = b.category', 'LEFT');
        $this->db->order_by('b.id', 'DESC');
        $this->db->group_by('b.id');
        
        if ($total == 1) {
            $query = $this->db->get();
            $query = $query->num_rows();
            return $query;
        } else {
            $query = $this->db->get('', $limit, $offset);
            $query = $query->result();
            return $query;
        }
    }



    //get latest users
    function get_all_locations(){
        $this->db->select('l.*');
        $this->db->from('locations l');
        $this->db->where('l.status', 1);
        $this->db->where('l.parent_id', 0);
        $this->db->group_by('l.name');
        $this->db->order_by('l.name','ASC');
        $query = $this->db->get();
        $query = $query->result();
        return $query;
    }



    // get images by user
    function get_total_info(){
        $this->db->select('p.id');
        $this->db->select('(SELECT count(posts.id)
                            FROM posts 
                            WHERE (status = 1)
                            )
                            AS post',TRUE);
        
        $this->db->select('(SELECT count(users.id)
                            FROM users 
                            WHERE (status = 1)
                            )
                            AS user',TRUE);

        $this->db->from('posts p');
        $query = $this->db->get();
        $query = $query->row();
        return $query;
    }


     //get user info
    function get_user_info()
    {
        $this->db->select('u.*');
        $this->db->from('users u');
        $this->db->where('u.id', $this->session->userdata('id'));
        $query = $this->db->get();
        $query = $query->row();  
        return $query;
    }


    // image upload function with resize option
    function upload_image($max_size){
            
            // set upload path
            $config['upload_path']  = "./uploads/";
            $config['allowed_types']= 'gif|jpg|png|jpeg';
            $config['max_size']     = '92000';
            $config['max_width']    = '92000';
            $config['max_height']   = '92000';

            $this->load->library('upload', $config);

            if ($this->upload->do_upload("photo")) {

                
                $data = $this->upload->data();

                // set upload path
                $source             = "./uploads/".$data['file_name'] ;
                $destination_thumb  = "./uploads/thumbnail/" ;
                $destination_medium = "./uploads/medium/" ;
                $main_img = $data['file_name'];
                // Permission Configuration
                chmod($source, 0777) ;

                /* Resizing Processing */
                // Configuration Of Image Manipulation :: Static
                $this->load->library('image_lib') ;
                $img['image_library'] = 'GD2';
                $img['create_thumb']  = TRUE;
                $img['maintain_ratio']= TRUE;

                /// Limit Width Resize
                $limit_medium   = $max_size ;
                $limit_thumb    = 150;

                // Size Image Limit was using (LIMIT TOP)
                $limit_use  = $data['image_width'] > $data['image_height'] ? $data['image_width'] : $data['image_height'] ;

                // Percentase Resize
                if ($limit_use > $limit_medium || $limit_use > $limit_thumb) {
                    $percent_medium = $limit_medium/$limit_use ;
                    $percent_thumb  = $limit_thumb/$limit_use ;
                }

                //// Making THUMBNAIL ///////
                $img['width']  = $limit_use > $limit_thumb ?  $data['image_width'] * $percent_thumb : $data['image_width'] ;
                $img['height'] = $limit_use > $limit_thumb ?  $data['image_height'] * $percent_thumb : $data['image_height'] ;

                // Configuration Of Image Manipulation :: Dynamic
                $img['thumb_marker'] = '_thumb-'.floor($img['width']).'x'.floor($img['height']) ;
                $img['quality']      = ' 100%' ;
                $img['source_image'] = $source ;
                $img['new_image']    = $destination_thumb ;

                $thumb_nail = $data['raw_name']. $img['thumb_marker'].$data['file_ext'];
                // Do Resizing
                $this->image_lib->initialize($img);
                $this->image_lib->resize();
                $this->image_lib->clear() ;

                ////// Making MEDIUM /////////////
                $img['width']   = $limit_use > $limit_medium ?  $data['image_width'] * $percent_medium : $data['image_width'] ;
                $img['height']  = $limit_use > $limit_medium ?  $data['image_height'] * $percent_medium : $data['image_height'] ;

                // Configuration Of Image Manipulation :: Dynamic
                $img['thumb_marker'] = '_medium-'.floor($img['width']).'x'.floor($img['height']) ;
                $img['quality']      = '100%' ;
                $img['source_image'] = $source ;
                $img['new_image']    = $destination_medium ;

                $mid = $data['raw_name']. $img['thumb_marker'].$data['file_ext'];
                // Do Resizing
                $this->image_lib->initialize($img);
                $this->image_lib->resize();
                $this->image_lib->clear() ;

                // set upload path
                $images = 'uploads/medium/'.$mid;
                $thumb  = 'uploads/thumbnail/'.$thumb_nail;
                unlink($source) ;

                return array(
                    'images' => $images,
                    'thumb' => $thumb
                );
            }
            else {
                echo "Failed! to upload image" ;
            }
            
    }


    //multiple image upload with resize option
    public function do_upload($photo) {                   
        $config['upload_path']  = "./uploads/";
        $config['allowed_types']= 'gif|jpg|png|jpeg';
        $config['max_size']     = '20000';
        $config['max_width']    = '20000';
        $config['max_height']   = '20000';
 
        $this->load->library('upload', $config);                
        
            if ($this->upload->do_upload($photo)) {
                $data       = $this->upload->data(); 
                /* PATH */
                $source             = "./uploads/".$data['file_name'] ;
                $destination_thumb  = "./uploads/thumbnail/" ;
                $destination_medium = "./uploads/medium/" ;
                $destination_big    = "./uploads/big/" ;

                // Permission Configuration
                chmod($source, 0777) ;

                /* Resizing Processing */
                // Configuration Of Image Manipulation :: Static
                $this->load->library('image_lib') ;
                $img['image_library'] = 'GD2';
                $img['create_thumb']  = TRUE;
                $img['maintain_ratio']= TRUE;

                /// Limit Width Resize
                $limit_big   = 1000 ;
                $limit_medium    = 400 ;
                $limit_thumb    = 100 ;

                // Size Image Limit was using (LIMIT TOP)
                $limit_use  = $data['image_width'] > $data['image_height'] ? $data['image_width'] : $data['image_height'] ;

                // Percentase Resize
                if ($limit_use > $limit_big || $limit_use > $limit_thumb || $limit_use > $limit_medium) {
                    $percent_big = $limit_big/$limit_use ;
                    $percent_medium  = $limit_medium/$limit_use ;
                    $percent_thumb  = $limit_thumb/$limit_use ;
                }

                //// Making THUMBNAIL ///////
                $img['width']  = $limit_use > $limit_thumb ?  $data['image_width'] * $percent_thumb : $data['image_width'] ;
                $img['height'] = $limit_use > $limit_thumb ?  $data['image_height'] * $percent_thumb : $data['image_height'] ;

                // Configuration Of Image Manipulation :: Dynamic
                $img['thumb_marker'] = '_thumb-'.floor($img['width']).'x'.floor($img['height']) ;
                $img['quality']      = '99%' ;
                $img['source_image'] = $source ;
                $img['new_image']    = $destination_thumb ;

                $thumb_nail = $data['raw_name']. $img['thumb_marker'].$data['file_ext'];
                // Do Resizing
                $this->image_lib->initialize($img);
                $this->image_lib->resize();
                $this->image_lib->clear() ;                 

                //// Making MEDIUM ///////
                $img['width']  = $limit_use > $limit_medium ?  $data['image_width'] * $percent_medium : $data['image_width'] ;
                $img['height'] = $limit_use > $limit_medium ?  $data['image_height'] * $percent_medium : $data['image_height'] ;

                // Configuration Of Image Manipulation :: Dynamic
                $img['thumb_marker'] = '_medium-'.floor($img['width']).'x'.floor($img['height']) ;
                $img['quality']      = '99%' ;
                $img['source_image'] = $source ;
                $img['new_image']    = $destination_medium ;

                $medium = $data['raw_name']. $img['thumb_marker'].$data['file_ext'];
                // Do Resizing
                $this->image_lib->initialize($img);
                $this->image_lib->resize();
                $this->image_lib->clear() ;               

                ////// Making BIG /////////////
                $img['width']   = $limit_use > $limit_big ?  $data['image_width'] * $percent_big : $data['image_width'] ;
                $img['height']  = $limit_use > $limit_big ?  $data['image_height'] * $percent_big : $data['image_height'] ;

                // Configuration Of Image Manipulation :: Dynamic
                $img['thumb_marker'] = '_big-'.floor($img['width']).'x'.floor($img['height']) ;
                $img['quality']      = '99%' ;
                $img['source_image'] = $source ;
                $img['new_image']    = $destination_big ;

                $album_picture = $data['raw_name']. $img['thumb_marker'].$data['file_ext'];
                // Do Resizing
                $this->image_lib->initialize($img);
                $this->image_lib->resize();
                $this->image_lib->clear() ;

                $data_image = array(
                    'thumb' => 'uploads/thumbnail/'.$thumb_nail,
                    'medium' => 'uploads/medium/'.$medium,
                    'big' => 'uploads/big/'.$album_picture
                );

                unlink($source) ;   
                return $data_image;   
    
            }
            else {
                return FALSE ;
            }
       
    }

}