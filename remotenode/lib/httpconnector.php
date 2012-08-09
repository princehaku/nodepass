<?php
/*  Copyright 2012 princehaku
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *  limitations under the License.
 *
 *  Created on : 2012-08-09
 *  Last Update: 2012-08-09
 *  Author     : princehaku
 *  Site       : http://3haku.net
 *
 * 
 */
/**用curl做的联网类
 */
class httpconnector {
    /**Curl类
     *
     */
    private $curl;
    /**cookie字符串
     */
    private $cookie;
    /**源(用于最后结果调试)
     */
    private $sourceWmlStack = array();
    /**得到源wml栈
     */
    public function getSource() {
        return $this->sourceWmlStack;
    }
    /**get方式下载网页内容
     *@param $url
     *@return web conntent
     */
    public function get($url) {
        
        $this->curl = curl_init();
        
        curl_setopt($this->curl, CURLOPT_URL, $url);
        
        // 设置header
        curl_setopt($this->curl, CURLOPT_HEADER, 1);
        curl_setopt($this->curl, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
        curl_setopt($this->curl, CURLOPT_COOKIE, $this->cookie);
        
        // 设置cURL 参数，要求结果保存到字符串中还是输出到屏幕上。
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
        
        // 运行cURL，请求网页
        $data = curl_exec($this->curl);
        // 关闭URL请求
        curl_close($this->curl);
        // 找到cookie 放入cookiestring
        preg_match_all("/Set-Cookie:(.*?);/", $data, $match, PREG_SET_ORDER);
        foreach ($match as $r) {
            if ($this->cookie != '') {
            	$this->cookie = $this->cookie . ';';
            }
            if (isset($r[1])) {
                $this->cookie .= trim(str_replace("\r\n", "", $r[1]));
            }
        }
        //放入调试栈
        array_push($this->sourceWmlStack, " [$url] " . $data);
        
        return $data;
    
    }
    
    /**POST方式下载网页内容
     *@param $url
     *@param $params post的信息串
     *@return web conntent
     */
    public function post($url, $params) {
        
        $this->curl = curl_init();
        
        curl_setopt($this->curl, CURLOPT_URL, $url);
        
        // 设置header
        curl_setopt($this->curl, CURLOPT_HEADER, 1);
        curl_setopt($this->curl, CURLOPT_COOKIE, $this->cookie);
        curl_setopt($this->curl, CURLOPT_POST, 1);
        curl_setopt($this->curl, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $params);
        
        // 设置cURL 参数，要求结果保存到字符串中还是输出到屏幕上。
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
        
        // 运行cURL，请求网页
        $data = curl_exec($this->curl);
        
        // 关闭URL请求
        curl_close($this->curl);
        // 找到cookie 放入cookiestring
        preg_match_all("/Set-Cookie:(.*?);/", $data, $match, PREG_SET_ORDER);
        
        foreach ($match as $r) {
            if ($this->cookie != '') {
            	$this->cookie = $this->cookie . ';';
            }
            if (isset($r[1])) {
                $this->cookie .= trim(str_replace("\r\n", "", $r[1]));
            }
        }
        
        //放入调试栈
        array_push($this->sourceWmlStack, " [$url] " . $data);
        
        return $data;
    
    }
}