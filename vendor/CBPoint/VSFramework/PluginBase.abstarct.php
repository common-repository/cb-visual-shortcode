<?php
/**
* 
*/

// No direct access
defined('ABSPATH') or die();

/**
* 
*/
abstract class CBVSPluginBase
{
    
    const CONFIG_SEC_PLUGIN = 'plugin';
    
    const EVENT_ACTIVATE = 'activate';
    const EVENT_DEACTIVATE = 'deactivate';
    const EVENT_UNINSTALL = 'uninstall';
    
    const ENV_STATE = self::ENV_STATE_DEV;
    const ENV_STATE_DEV = 'dev';
    const ENV_STATE_PRE_PRO = 'pre-pro';
    const ENV_STATE_PRO = 'pro';
    
    
    /**
    * put your comment there...
    * 
    * @var mixed
    */
    private $config;
    
    /**
    * put your comment there...
    * 
    * @var mixed
    */
    private $dir;
    
    /**
    * put your comment there...
    * 
    * @var mixed
    */
    protected $events;
    
    /**
    * put your comment there...
    * 
    * @var mixed
    */
    private $file;
    
    /**
    * put your comment there...
    * 
    * @var mixed
    */
    private $fileName;
    
    /**
    * put your comment there...
    * 
    * @var mixed
    */
    private $modules = array();
    
    /**
    * put your comment there...
    * 
    * @var mixed
    */
    private $name;
    
    /**
    * put your comment there...
    * 
    * @var mixed
    */
    private $pluginClass;
    
    /**
    * put your comment there...
    * 
    * @var mixed
    */
    private $services = array();
    
    /**
    * put your comment there...
    * 
    * @var mixed
    */
    private $textDomain;
    
    /**
    * put your comment there...
    * 
    * @var mixed
    */
    private $url;
    
    /**
    * put your comment there...
    * 
    * @var mixed
    */
    private $urlScripts;
    
    /**
    * put your comment there...
    * 
    * @var mixed
    */
    private $urlStyles;
    
    /**
    * put your comment there...
    * 
    * @var mixed
    */
    private static $instances;
    
    /**
    * put your comment there...
    * 
    * @param mixed $text
    */
    public function __($text)
    {
        
        // Call $this->getText() with all passed args + text domain as first parameter
        $args = func_get_args();
        
        array_unshift($args, $this->textDomain);
        
        $localText = call_user_func_array(
            array($this, 'getText'),
            $args
        );
        
        return $localText;
    }

    /**
    * put your comment there...
    * 
    * @param mixed $file
    * @param mixed $config
    * @return CBVSPlugin
    */
    protected function __construct($file, $config) 
    {
        
        $this->events = new CBVSEvents($this);
        $this->pluginClass = get_class($this);
        
        // INit
        $this->file = $file;
        $this->fileName = basename($file);
        list($this->name, ) = explode('.', $this->fileName);
        $this->dir = dirname($file);
        $this->url = plugin_dir_url($this->getFile());
        
        // Config
        $this->config =& $config;
        
        $this->textDomain = $this->config['config']['local']['textDomain'];
        
        // Core events
        $this->bindCoreEvents();
    }

    /**
    * put your comment there...
    * 
    * @param mixed $text
    */
    public function _e($text)
    {
        
        $localTxt = call_user_func_array(array($this, '__'), func_get_args());
        
        echo $localTxt;
    }
    
    /**
    * put your comment there...
    * 
    */
    public function _localize()
    {
        
        // Localize plugin/module
        $localConfig =& $this->config['config']['local'];

        load_plugin_textdomain(
            $this->textDomain, 
            false, 
            $this->name . DIRECTORY_SEPARATOR . $localConfig['dir']
        );
    }

    /**
    * put your comment there...
    * 
    */
    public function _OnActivate()
    {
        $this->events->trigger(self::EVENT_ACTIVATE);
    }

    /**
    * put your comment there...
    * 
    */
    public function _OnDeactivate()
    {
        $this->events->trigger(self::EVENT_DEACTIVATE);
    }
    
    /**
    * put your comment there...
    * 
    */
    protected function _OnUninstall()
    {
        $this->events->trigger(self::EVENT_UNINSTALL);
    }
    
    /**
    * put your comment there...
    *     
    */
    public static function _OnUninstallProxy()
    {
        
        // Dispatch Uninstall call to the target plugin
        $class = get_called_class();
        
        $plugin =& self::getPlugin($class);
        
        $plugin->_OnUninstall();
    }
    
    /**
    * put your comment there...
    * 
    * @param CBVSServiceControllerBase $serviceController
    * @return CBVSServiceControllerBase
    */
    public function & addServiceController(CBVSServiceControllerBase & $serviceController)
    {
        
        $this->services[get_class($serviceController)] =& $serviceController;
        
        return $this;
    }
    
    /**
    * put your comment there...
    * 
    */
    protected function bindCoreEvents()
    {
        
        register_activation_hook($this->getFile(), array($this, '_OnActivate'));
        register_deactivation_hook($this->getFile(), array($this, '_OnDeactivate'));
        
        if ($this->config['uninstallable'])
        {
            register_uninstall_hook($this->getFile(), array($this->pluginClass, '_OnUninstallProxy'));    
        }
        
    }
    
    /**
    * put your comment there...
    * 
    */
    public function & db()
    {
        static $db;
        
        if (!$db)
        {
            
            $db = new CBVSDatabase($GLOBALS['wpdb']);
            
            // Configure Database object table names
            $config = $this->getConfig();
            
            $db->addTables($config['db']['tables'], $config['db']['tablePrefix']);
        }
        
        return $db;
    }
    
    /**
    * put your comment there...
    * 
    */
    public function & defineServicesController()
    {
        
        $servicesClass = func_get_args();
        
        foreach ($servicesClass as $serviceClass)
        {
            
            $serviceObject = call_user_func(
                array($serviceClass, 'run'),
                $this
            );
            
            $this->services[$serviceClass] = $serviceObject;
        }
        
        return $this;
    }

    
    /**
    * put your comment there...
    * 
    * @param mixed $serviceController
    * @return mixed
    */
    public function dispatchRequestedAJAXAction(& $serviceController)
    {
        
        $result = null;
        
        try
        {
            
            $result = $this->dispatchRequestedAction(
                $serviceController, 
                $dispatchInfo
            );
            
            // HTTP Success Header
            header('HTTP:1/1 200 OK');
        }
        catch (Exception $exception)
        {
            
            // HTTP INternal Server error
            header('HTTP/1.1: 500 Internal Server Error');
            
            throw $exception;
        }

        // Render Ajax Response based on inpput format
        $response = $this->renderRequestedView(
            $serviceController,
            $dispatchInfo,
            $result
        );
        
        return $response;
    }

    /**
    * put your comment there...
    * 
    * @param mixed $serviceController
    * @param mixed $dispathInfo
    */
    public function dispatchRequestedAction(& $serviceController, & $dispathInfo = null)
    {
        
        $config =& $this->config;
        
        // Read MVC Configs for current service controller
        $ivp = $config['config']['slugNamespace']; // Input Var Prefix
        $mvcConfig = $config['mvc'];
        $controllers =& $mvcConfig['controllers'];
        $serviceCtrConfig =& $mvcConfig['serviceControllers'][get_class($serviceController)]; 
        
        // Input vars name
        $controllerVarName = "{$ivp}-rcontroller";
        $viewVarName = "{$ivp}-rview";
        $actionVarName = "{$ivp}-raction";
        $templateVarName = "{$ivp}-rtemplate";
        
        # Select template name based on requested view
        $viewName =   (isset($_GET[$viewVarName]) && $_GET[$viewVarName]) ? 
                            $_GET[$viewVarName] : 
                            (
                                (isset($serviceCtrConfig['view']) && $serviceCtrConfig['view']) ?
                                $serviceCtrConfig['view'] :
                                null
                            );
                                                        
        $controllerName =   (isset($_GET[$controllerVarName]) && $_GET[$controllerVarName]) ? 
                            $_GET[$controllerVarName] : 
                            (
                                $viewName ? 
                                $viewName :
                                $serviceCtrConfig['controller'] 
                            );
        
        // Either Controller or view might be supplied, make sure to 
        // have both equal if one if not supplied
        if (!$viewName)
        {
            $viewName = $controllerName;
        }
        
        $actionName =   (isset($_GET[$actionVarName]) && $_GET[$actionVarName]) ? 
                        $_GET[$actionVarName] : $serviceCtrConfig['action'];
        
        $templateName = (isset($_GET[$templateVarName]) && $_GET[$templateVarName]) ? 
                        $_GET[$templateVarName] : 
                        (
                            isset($serviceMvcConfig['template']) ? 
                            $serviceMvcConfig['template'] :
                            $actionName
                        );
                            
        # Create Controller and Dispatch action
        if (!isset($controllers[$controllerName]))
        {
            throw new Exception("{$controllerName} Controller doesnt exists");
        }
        
        // Set MVC Config class namespace if not set
        // This is because MVC configs is not currently inherited
        if (!isset($mvcConfig['namespace']))
        {
            $mvcConfig['namespace'] = $config['config']['namespace'];
        }
        
        // Create controller            
        $controllerClass = $controllers[$controllerName]['class'];
        $controller = new $controllerClass( $mvcConfig,
                                            $dispathInfo,
                                            $serviceController,
                                            $this);
        
        // Set Dispath infor sturcture back
        $dispathInfo['controllerName'] = $controllerName;
        $dispathInfo['viewName'] = $viewName;
        $dispathInfo['actionName'] = $actionName;
        $dispathInfo['templateName'] = $templateName;
        
        $dispathInfo['controller'] =& $controller;
        $dispathInfo['serviceController'] =& $serviceController;
        
        // Dispatch action
        $result = $controller->dispatchAction($actionName);
        
        // Return Result
        return $result;
    }

    /**
    * put your comment there...
    * 
    * @param mixed $dir
    * @param mixed $config
    */
    public static function envLoadConfig($pluginDir, $config)
    {
        
        $configFileName = $config['env']['states'][$config['env']['state']]['config'];
        $configFile = $pluginDir . DIRECTORY_SEPARATOR . $config['env']['configsDir'] .
                      DIRECTORY_SEPARATOR .  $configFileName;
        
        if (!file_exists($configFile))
        {
            throw new Exception("Could not load Config file {$configFile}");
        }
        
        $envConfig = require $configFile;
        
        $envConfig = array_merge_recursive($config, $envConfig);
        
        return $envConfig;
    }
    
    /**
    * put your comment there...
    * 
    * @param mixed $baseUri
    * @param mixed $routeNames
    * @param mixed $actions
    * @param mixed $routes
    * @return CBVSPluginBase
    */
    public function & getRoutes($baseUri, $routeNames, $actions, & $routes)
    {

        $router =& $this->router();
        $varsList = array();
        
        foreach ($actions as $actionName => $actionParams)
        {
            
            // Convert Route Name string to RouteName => array()
            if (!is_array($actionParams))
            {
                $actionName = $actionParams;
                $actionParams = array();
            }
            
            // Vars List
            $varsList['${KEY}'] = $actionName;
            
            // Build Common Parameters
            foreach ($routeNames as $routeName => $routeValue)
            {
                $actionParams[$routeName] = isset($varsList[$routeValue]) ? 
                                            $varsList[$routeValue] :
                                            $routeValue;    
            }
            
            // Create routes
            $routes[$actionName] = $router->buildUri($baseUri, $actionParams);
        }
        
        return $this;
    }
    
    /**
    * put your comment there...
    * 
    */
    public function & getConfig()
    {
        return $this->config;
    }
    
    /**
    * put your comment there...
    * 
    * @param mixed $name
    */
    public function getConfigSection($name)
    {
        return $this->config[$name];
    }
    
    /**
    * put your comment there...
    * 
    */
    public function getDir()
    {
        return $this->dir;
    }
    
    /**
    * put your comment there...
    * 
    */
    protected function & getEvents()    
    {
        return $this->events;
    }
    
    /**
    * put your comment there...
    * 
    */
    public function getFile()
    {
        return $this->file;
    }

    /**
    * put your comment there...
    * 
    */
    public function getFileName()
    {
        return $this->fileName;
    }
    
    /**
    * put your comment there...
    * 
    * @param mixed $name
    */
    public function & getModule($name)
    {
        
        if (!isset($this->modules[$name]))
        {
            throw new Exception("{$name} module doesnt exists!");
        }
        
        return $this->modules[$name];
    }
    
    /**
    * put your comment there...
    * 
    */
    public function getModules()
    {
        return $this->modules;
    }
    
    /**
    * put your comment there...
    * 
    */
    public function getName()
    {
        return $this->name;
    }
    
    /**
    * put your comment there...
    * 
    * @param mixed $class
    */
    public static function & getPlugin($class)
    {
        
        if (!isset(self::$instances[$class]))
        {
            throw new Exception("{$class} Plugin does nto exists!!");
        }
        
        $instance =& self::$instances[$class];
        
        return $instance;
    }
    
    /**
    * put your comment there...
    * 
    */
    public function getPluginConfig()
    {
        return $this->getConfigSection(self::CONFIG_SEC_PLUGIN);
    }
    
    /**
    * put your comment there...
    * 
    * @param mixed $name
    */
    public function & getServiceController($name)
    {
        
        if (!isset($this->services[$name]))
        {
            throw new Exception("{$name} Service Controller doesnt exists!");
        }
        
        return $this->services[$name];
    }
    
    /**
    * put your comment there...
    * 
    */
    public function getSlugNamespace()
    {
        
        $slugNs = $this->config['config']['slugNamespace'];
        
        return $slugNs;
    }
    
    /**
    * put your comment there...
    * 
    * @param mixed $text
    * @param mixed $domain
    */
    public function getText($domain, $text)
    {
        
        $localText = __($text, $domain);
        
        // Placholder as all args after excluding domain and text args
        $args = array_slice(func_get_args(), 1);
        
        if (!empty($args))
        {
            
            $args[0] = $localText;
            
            $localText = call_user_func_array('sprintf', $args);
        }
        
        return $localText;
    }
    
    /**
    * put your comment there...
    * 
    */
    public function getTextDomain()
    {
        return $this->textDomain;
    }
    
    /**
    * put your comment there...
    * 
    */
    public function & html()
    {
        
        static $html;
        
        if (!$html)
        {
            $html = new CBVSHTMLDocument();
        }
        
        return $html;
    }
    
    /**
    * put your comment there...
    * 
    * @param mixed $configName
    */
    public function loadConfig($configName)
    {
        
        static $configs = array();
        
        if (!isset($configs[$configName]))
        {
            // Load config file
            $configDir = $this->config['config']['configDir'];
            
            $configFile =   $this->dir . DIRECTORY_SEPARATOR .
                            $configDir . DIRECTORY_SEPARATOR .
                            "{$configName}.config.php";
            
            // Return configuration
            $configs[$configName] = require $configFile;            
        }
        
        return $configs[$configName];
    }
    
    /**
    * put your comment there...
    * 
    */
    protected function & loadModules()
    {
        
        // Init
        $modulesConfig =& $this->config['modules'];
        $modulesDir = $this->getDir() . DIRECTORY_SEPARATOR . $modulesConfig['dir'];
        $configDir = $this->config['config']['configDir'];
        
        // Load all modules
        $modulesNamespace = $modulesConfig['namespace'];
        
        foreach ($modulesConfig['list'] as $moduleName => $moduleData)
        {
            
            // Build module file path
            $moduleFileRelPath =    $moduleData['dirName'] .
                                    DIRECTORY_SEPARATOR . "Plugin-Module-{$moduleData['dirName']}.class.php";
                                    
            $moduleFileAbsPath =    $modulesDir . DIRECTORY_SEPARATOR . $moduleFileRelPath;
            
            // Module config file
            $moduleConfigFilePath = $modulesDir . DIRECTORY_SEPARATOR . 
                                    $moduleData['dirName'] . DIRECTORY_SEPARATOR .
                                    $configDir . DIRECTORY_SEPARATOR .
                                    'Config.php';
                                    
            $moduleConfig =         require $moduleConfigFilePath;
                                    
            $moduleConfig['env']  +=  $this->config['env'];
            
            // Module Env Config File 
            $moduleEnvConfigFilePath =  $modulesDir . DIRECTORY_SEPARATOR . $moduleData['dirName'] .
                                        DIRECTORY_SEPARATOR . $moduleConfig['env']['configsDir'] .
                                        DIRECTORY_SEPARATOR . $moduleConfig['env']['states'][$moduleConfig['env']['state']]['config'];
            
            $moduleConfig = array_merge_recursive($moduleConfig, require $moduleEnvConfigFilePath);
            
            // Merge only config section from parent plugin/module
            $moduleConfig['config'] = array_merge(
                $this->config['config'], 
                $moduleConfig['config']
            );
                                        
            // get Module class
            $moduleClass = "{$modulesNamespace}{$moduleName}";
            
            // Load module
            $module = call_user_func(
                array($moduleClass, 'Plug'),
                $moduleFileAbsPath,
                $moduleConfig
            );
            
            // Bind module
            $module->bind($this);
            
            // Hold module pointer
            $this->modules[$moduleName] = $module;
            
        }
        
        return $this;
    }
    
    /**
    * put your comment there...
    * 
    * @return BOOL|Exception True if installed successed or Exception if installation faild
    */
    protected function init() 
    {
        
        // Out if not installer defined
        if (!isset($this->config['installer']) || 
            !$this->config['installer'])
        {
            return true;
        }
        
        // Get installer instance
        $installerClass = $this->config['installer'];
        $installer = call_user_func(array($installerClass, 'create'));
        
        // Install
        if ($installer->isInstalled() != CBVSInstallerBase::INSTALLED)
        {
            try
            {
                // Install
                $installer->install();
            }
            catch (Exception $exception)
            {
                return $exception;
            }
        }
        
        return true;
        
    }
    
    /**
    * put your comment there...
    * 
    * @return CBVSPluginBase
    */
    public static function & me()
    {
        
        $instance = self::getPlugin(get_called_class());
        
        return $instance;
    }

    /**
    * put your comment there...
    * 
    * @param mixed $callback
    */
    public function & onActivate($callback)
    {
        
        $this->events->bind(self::EVENT_ACTIVATE, $callback);
        
        return $this;
    }
    
    /**
    * put your comment there...
    * 
    * @param mixed $callback
    */
    public function & onDeactivate($callback)
    {
        
        $this->events->bind(self::EVENT_DEACTIVATE, $callback);
        
        return $this;
    }
    
    /**
    * put your comment there...
    * 
    * @param mixed $callback
    */
    public function & onUninstall($callback)
    {
        
        $this->events->bind(self::EVENT_ACTIVATE, $callback);
        
        return $this;
    }
    
    /**
    * put your comment there...
    * 
    * @param mixed $file
    * @param mixed $config
    */
    public static function & plug($file, $config)
    {
        
        $pluginClass = get_called_class();
        
        if (!isset(self::$instances[$pluginClass]))
        {
            
            // Instantiate
            $instance = new $pluginClass($file, $config);
            $instance->init();
            
            // Cache it!
            self::$instances[$pluginClass] = $instance;
        }
        
        return self::$instances[$pluginClass];
    }
    
    /**
    * put your comment there...
    * 
    * @param mixed $srvController
    * @param mixed $dispatchInfo
    * @param mixed $vars
    */
    public function renderRequestedView(& $srvController, 
                                        & $dispatchInfo, 
                                        $result = null)
    {
        
        $response = '';
        
        // Render View based on the requested format
        $srvConfig =& $this->config['mvc']['serviceControllers'][get_class($srvController)];
        $ivp = $this->config['config']['slugNamespace'];
        
        // Getting format based on the input vars
        $formatVarName = "{$ivp}-rformat";
        
        $formatName =   isset($_GET[$formatVarName]) && $_GET[$formatVarName] ?
                        $_GET[$formatVarName] :
                        $srvConfig['format'];
                        
        
        // Render view based on format
        switch ($formatName)
        {
            
            case 'xml': break;
            
            case 'json':
            case 'text/json':
            
                // Push messsages
                $result['messages'] = $dispatchInfo['serviceController']->getCleanMessages();
                
                $response = json_encode($result);
                
            break;
            
            case 'attachment':
            
                $attachmentView = new CBVSViewAttachment($result);
                
                echo $attachmentView;
            
            break;
            
            case 'plain':
            case 'text/plain':
            
                $response = $result;
                
            break;
            
            case 'html':
            case 'text/html':
            default: // HTML
            
                // Default Renderer helper
                // Regullar to be used for displaying error/notice/wanring messages
                $result['rh'] = new CBVSViewRendererHelper($dispatchInfo['serviceController']->getCleanMessages());
                
                // Render HTML View!
                $viewFullName = "{$dispatchInfo['viewName']}:{$dispatchInfo['templateName']}";            
        
                $response = $this->renderView(
                    $viewFullName,
                    $result
                );
                
            break;
        }
                        
        // Write Service Controller, Controller and Models states
        $dispatchInfo['controller']->write();
        
        // Write all Defined Service Controllers as well
        $this->writeServiceControllers();
                
        return $response;
    }
    
    /**
    * put your comment there...
    * 
    * @param mixed $path
    * @param mixed $vars
    */
    public function renderTemplate($__tmpPath__, $vars = array())
    {
        
        $config =& $this->config;
        $tmpExtension = $config['view']['extension'];
        
        $__tmpPath__ = str_replace(
            '/', 
            DIRECTORY_SEPARATOR, 
            $__tmpPath__
        );
        
        $templateFilePath = $this->getDir() . DIRECTORY_SEPARATOR . "{$__tmpPath__}.{$tmpExtension}";
        
        if (!file_exists($templateFilePath))
        {
            throw new Exception("{$templateFilePath} Template file doesnt exists!");
        }
        
        if ($vars)
        {
            # Extract template vars to be simply accessible by template
            extract($vars);
        }
        
        # Reneder template
        ob_start();
        
        require $templateFilePath;
    
        $template = ob_get_clean();
        
        return $template;
    }
    
    /**
    * put your comment there...
    * 
    * @param mixed $name
    * @param mixed $vars
    */
    public function renderView($name, $vars = array())
    {
        
        // Get view path
        $config =& $this->config;
        
        if (!isset($config['view']['views'][$name]))
        {
            throw new Exception("{$name} Template could not be loaded!!!");
        }
        
        $content = $this->renderTemplate($config['view']['views'][$name], $vars);
        
        return $content;
    }
    
    /**
    * put your comment there...
    * 
    */
    public function & router()
    {
        
        static $router;
        
        if (!$router)
        {
            
            if (!$this->config['config']['router']['class'])
            {
                throw new Exception('Router is not configured');
            }
            
            $routerClass = $this->config['config']['router']['class'];
            $router = new $routerClass();
        }
        
        return $router;
    }
    
    /**
    * put your comment there...
    * 
    * @param mixed $path
    */
    public function url($path = '')
    {
        
        if ($path)
        {
            $path .= "/{$path}";
        }
        
        $url = "{$this->url}{$path}";
        
        return $url;
    }

    /**
    * put your comment there...
    * 
    * @param mixed $style
    */
    public function urlScript($scriptPath)
    {
        
        $url = "{$this->urlScripts}/{$scriptPath}";
        
        return $url;
    }
        
    /**
    * put your comment there...
    * 
    * @param mixed $style
    */
    public function urlStyle($stylePath)
    {
        
        $url = "{$this->urlStyles}/{$stylePath}";
        
        return $url;
    }
    
    /**
    * put your comment there...
    * 
    */
    protected function & useLocalization()
    {
        
        add_action('plugins_loaded', array($this, '_localize'));
        
        return $this;
    }
    
    /**
    * put your comment there...
    * 
    */
    protected function & useResources()
    {
        
        // Resources Urls
        $this->urlScripts = "{$this->url}{$this->config['resource']['scriptsPath']}";
        $this->urlStyles = "{$this->url}{$this->config['resource']['stylesPath']}";
        
        return $this;
    }
    
    /**
    * put your comment there...
    * 
    */
    public function & writeServiceControllers()
    {
                
        foreach ($this->services as $serviceController)
        {
            $serviceController->write();
        }
        
        return $this;
    }
    
}