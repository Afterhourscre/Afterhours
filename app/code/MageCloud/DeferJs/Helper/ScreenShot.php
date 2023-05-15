<?php
/**
 * @author andy
 * @email andyworkbase@gmail.com
 * @team MageCloud
 */
namespace MageCloud\DeferJs\Helper;

/**
 * Class ScreenShot
 */
class ScreenShot
{
    /**
     * ScreenshotLayer API key (@see https://screenshotlayer.com/product)
     * @var string
     */
    private $apiKey = 'ece501a2bedcf826ea82bd4187978b42';

    /**
     * Api endpoint
     * @var string
     */
    private $endPoint = 'http://api.screenshotlayer.com/api/capture';

    /**
     * Secret keyword defined in screenshotLayer dashboard.
     * Leave blank if this feature was not activated.
     *
     * @var string
     */
    private $secretKey = '';

    /**
     * API params
     *
     * @var array
     */
    public $params = [];

    /**
     * Response capture
     *
     * @var string
     */
    public $capture;

    /**
     * Image Info
     *
     * @var
     */
    public $imageInfo;

    /**
     * ScreenShot constructor.
     *
     * @param string $url
     * @param string $format
     */
    public function __construct(
        $url = '',
        $format = 'PNG'
    ) {
        $this->params['url'] = $url;
        if (!empty($this->secretKey)) {
            $secret = md5($url . $this->secretKey);
            $this->params['secret_key'] = $secret;
        }
    }

    /**
     * This method will write an image tag with the correct request url.
     * Note: this method will not verify the request is to a valid url and
     * will return a broken image if not.
     *
     * @return string
     */
    public function getScreenShotUrl()
    {
        return $this->buildRequest();
    }

    /**
     * This method will query the api for the binary code of the captured webpage.
     *
     * @throws \Exception
     */
    public function capturePage()
    {
        $request = $this->buildRequest();
        $this->capture = file_get_contents($request);
        $this->imageInfo = getimagesizefromstring($this->capture);
        
        if (empty($this->imageInfo)) {
            if (empty($this->capture)) {
                throw new \Exception(__('An unknown error has occured'));
            } else {
                $response = json_decode($this->capture);
                throw new \Exception($response->error->info);
            }
        }
    }

    /**
     * This method will download the captured image to the client.
     *
     * @param string $fileName
     * @throws \Exception
     */
    public function downloadCapture($fileName = '')
    {
        $fileName = empty($fileName) ? 'capture' : $fileName;
        if (empty($this->capture)) {
            throw new \Exception(__('No image has been captured'));
        }
        
        header('Content-Type: ' . $this->imageInfo['mime']);
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Content-Transfer-Encoding: binary');
        
        echo $this->capture;
    }

    /**
     * This method will display the captured image to the browser.
     *
     * @return string
     * @throws \Exception
     */
    public function displayCapture()
    {
        if (empty($this->capture)) {
            throw new \Exception(__('No image has been captured'));
        }
        
        header('Content-Type: ' . $this->imageInfo['mime']);
        
        return $this->capture;
    }

    /**
     * This method will build the api request url.
     *
     * @return string
     * @throws \Exception
     */
    public function buildRequest()
    {
        if (empty($this->params['url'])) {
            throw new \Exception(__('API requires URL to video'));
        }
        
        $request = $this->endPoint . '?access_key=' . $this->apiKey;
        foreach ($this->params as $key => $value) {
            if ($key == 'url') {
                $request .= '&url=' . urlencode($value);
            } else {
                $request .= '&' . $key . '=' . $value;
            }
        }
        
        return $request;
    }

    /**
     * @param $key
     * @param $value
     */
    public function setParam($key, $value)
    {
        $this->params[$key] = $value;
    }
}
