<?php

App::uses('Controller', 'Controller');
App::uses('Helper', 'View');
App::uses('AppHelper', 'View/Helper');
App::uses('BootstrapFormHelper', 'TwitterBootstrap.View/Helper');
App::uses('HtmlHelper', 'View/Helper');
App::uses('FormHelper', 'View/Helper');
App::uses('ClassRegistry', 'Utility');
App::uses('Folder', 'Utility');

if (!defined('FULL_BASE_URL')) {
	define('FULL_BASE_URL', 'http://cakephp.org');
}

class TestBootstrapFormController extends Controller {

	public $name = 'TheTest';

	public $uses = null;
}

class Contact extends CakeTestModel {

	public $primaryKey = 'id';

	public $useTable = false;

	public $name = 'Contact';

	protected $_schema = array(
		'id' => array('type' => 'integer', 'null' => '', 'default' => '', 'length' => '8'),
		'name' => array('type' => 'string', 'null' => '', 'default' => '', 'length' => '255'),
		'email' => array('type' => 'string', 'null' => '', 'default' => '', 'length' => '255'),
		'phone' => array('type' => 'string', 'null' => '', 'default' => '', 'length' => '255'),
		'active' => array('type' => 'boolean', 'null' => '', 'deafault' => 1, 'length' => null),
		'password' => array('type' => 'string', 'null' => '', 'default' => '', 'length' => '255'),
		'published' => array('type' => 'date', 'null' => true, 'default' => null, 'length' => null),
		'created' => array('type' => 'date', 'null' => '1', 'default' => '', 'length' => ''),
		'updated' => array('type' => 'datetime', 'null' => '1', 'default' => '', 'length' => null),
		'age' => array('type' => 'integer', 'null' => '', 'default' => '', 'length' => null)
	);
}

/**
 * HtmlHelperTest class
 *
 * @package       Cake.Test.Case.View.Helper
 */
class BootstrapFormHelperTest extends CakeTestCase {

	public $BootstrapForm;
	public $View;

	public function setUp() {
		parent::setUp();
		$this->View = $this->getMock('View', array('addScript'), array(new TestBootstrapFormController()));

		$this->BootstrapForm = new BootstrapFormHelper($this->View);
		$this->BootstrapForm->request = new CakeRequest('contacts/add', false);
		$this->BootstrapForm->request->here = '/contacts/add';
		$this->BootstrapForm->request['action'] = 'add';
		$this->BootstrapForm->request->webroot = '';
		$this->BootstrapForm->request->base = '';

		ClassRegistry::addObject('Contact', new Contact());

		Configure::write('Asset.timestamp', false);
	}


	public function tearDown() {
		parent::tearDown();
		unset($this->BootstrapForm, $this->View);
	}

	/**
	 * test cases for BootstrapForm->create function
	 * @return void
	 */
	public function testCreate() {
		$expected = array(
			"form" => array(
				"action" => "/contacts/add",
				"id" => "ContactAddForm",
				"method" => "post",
				"accept-charset" => "utf-8",
				"class" => ""
			),
			"div" => array("style" => "display:none;"),
			"input" => array("type" => "hidden", "name" => "_method", "value" => "POST"),
			"/div"
		);
		$form = $this->BootstrapForm->create("Contact");
		$this->assertTags($form, $expected);

		$expected["form"]["class"] = "form-inline";
		$form = $this->BootstrapForm->create("Contact", array("formType" => "inline"));
		$this->assertTags($form, $expected);

		$expected["form"]["class"] = "form-inline some-class";
		$form = $this->BootstrapForm->create("Contact", array(
			"class"    => "some-class",
			"formType" => "inline"
		));
		$this->assertTags($form, $expected);
	}

	/**
	 * test cases for BootstrapForm->validButtonStyles function
	 * @return void
	 */
	public function testValidButtonStyles() {
		$expected = array(
			'button' => array('class' => 'btn', 'type' => 'submit'),
			'Submit',
			'/button'
		);
		$default = $this->BootstrapForm->button("Submit");
		$this->assertTags($default, $expected);
		$primary = $this->BootstrapForm->button("Submit", array("style" => "primary"));
		$expected['button']['class'] = 'btn btn-primary';
		$this->assertTags($primary, $expected);
	}

	/**
	 * test cases for BootstrapForm->validButtonSizes function
	 * @return void
	 */
	public function testValidButtonSizes() {
		$expected = array(
			'button' => array('class' => 'btn btn-mini', 'type' => 'submit'),
			'Submit',
			'/button'
		);
		$mini = $this->BootstrapForm->button("Submit", array("size" => "mini"));
		$this->assertTags($mini, $expected);
	}

	/**
	 * test cases for BootstrapForm->validButtonStylesAndSizes function
	 * @return void
	 */
	public function testValidButtonStylesAndSizes() {
		$expected = array(
			'button' => array('class' => 'btn btn-small btn-primary', 'type' => 'submit'),
			'Submit',
			'/button'
		);
		$mixed = $this->BootstrapForm->button(
			"Submit",
			array("style" => "primary", "size" => "small")
		);
		$this->assertTags($mixed, $expected);
	}

	/**
	 * test cases for BootstrapForm->validDisabledButton function
	 * @return void
	 */
	public function testValidDisabledButton() {
		$expected = array(
			'button' => array('class' => 'btn disabled', 'type' => 'submit'),
			'Submit',
			'/button'
		);
		$disabled = $this->BootstrapForm->button("Submit", array("disabled" => true));
		$this->assertTags($disabled, $expected);
	}

	/**
	 * test cases for BootstrapForm->invalidButtonStylesAndSizes function
	 * @return void
	 */
	public function testInvalidButtonStylesAndSizes() {
		$expected = array(
			'button' => array('class' => 'btn', 'type' => 'submit'),
			'Submit',
			'/button'
		);
		$invalid_size = $this->BootstrapForm->button("Submit", array("size" => "invalid"));
		$this->assertTags($invalid_size, $expected);
		$invalid_style = $this->BootstrapForm->button("Submit", array("style" => "invalid"));
		$this->assertTags($invalid_style, $expected);
	}

	/**
	 * test cases for BootstrapForm->validButtonForm function
	 * @return void
	 */
	public function testValidPostButton() {
		$expected = array(
			'form' => array(
				'method' => 'post',
				'action' => "/home",
				'accept-charset' => 'utf-8',
				'class' => ''
			),
			'div' => array("style" => "display:none;"),
			'input' => array('type' => 'hidden', 'name' => '_method', 'value' => 'POST'),
			"/div",
			'button' => array('class' => 'preg:/btn/', 'type' => 'submit'),
			'Link Text',
			'/button',
			'/form'
		);

		$result = $this->BootstrapForm->postButton("Link Text", "/home");
		$this->assertTags($result, $expected);

		$expected['button']['class'] = 'preg:/btn btn-small/';
		$result = $this->BootstrapForm->postButton(
			"Link Text",
			"/home",
			array("size" => "small")
		);
		$this->assertTags($result, $expected);

		$expected['button']['class'] = 'preg:/btn btn-danger/';
		$result = $this->BootstrapForm->postButton(
			"Link Text",
			"/home",
			array("style" => "danger")
		);
		$this->assertTags($result, $expected);

		$expected['button']['class'] = 'preg:/btn btn-large btn-success/';
		$result = $this->BootstrapForm->postButton(
			"Link Text",
			"/home",
			array("style" => "success", "size" => "large")
		);
		$this->assertTags($result, $expected);
	}

	/**
	 * test cases for BootstrapForm->validSubmit
	 * @return void
	 */
	public function testValidSubmit() {
		$expected = array(
			"div" => array("class" => "submit"),
			"input" => array("type" => "submit", "value" => "Submit", "class" => "btn"),
			"/div"
		);
		$default = $this->BootstrapForm->submit("Submit");
		$this->assertTags($default, $expected);
		$primary = $this->BootstrapForm->submit("Submit", array("style" => "primary"));
		$expected["input"]["class"] = "btn btn-primary";
		$this->assertTags($primary, $expected);
		$mini = $this->BootstrapForm->submit("Submit", array("size" => "mini"));
		$expected["input"]["class"] = "btn btn-mini";
		$this->assertTags($mini, $expected);
		$expected["input"]["class"] = "btn btn-small btn-primary";
		$mixed = $this->BootstrapForm->submit(
			"Submit",
			array("style" => "primary", "size" => "small")
		);
		$this->assertTags($mixed, $expected);
	}

}
