/*//////////////////////////////////////////////////////////////////////////////
 //
 //    Copyright (c) GreenPrint Technologies LLC. 2019
 //
 //    File Name         : RequestController.swift
 //
 //    File Created      : 27:Sept:2019
 //
 //    Dev Name          : Sachin Kumar K.
 //
 //    Description       : API Controller.
 //
 //////////////////////////////////////////////////////////////////////////// */

import Foundation
import UIKit
import SystemConfiguration
import Alamofire

class RequestController {

    static let shared = RequestController()
    
    let reachabilityManager = Alamofire.NetworkReachabilityManager(host: "www.google.com")

    func startNetworkReachabilityObserver() {
        reachabilityManager?.startListening(onUpdatePerforming: {
            status in
            switch status {
                case .reachable:
                    print("The network is reachable")
                    // When internet reachable update locally stored data to the server.
                    APIResponseHandler.updateLocalTasksToServer()
                
                case .notReachable:
                    print("The network is not reachable")
                
                case .unknown :
                    print("It is unknown whether the network is reachable")
            }
        })
    }
    
    /// Method used to request from API.
    /// - Parameters:
    ///     - params: Add key-value pair of dictionary.
    ///     - url: API URL to request.
    ///     - authKey: (Optional) add if request from autherized user.
    ///     - completion: Closure to handle the response.
//    static func requestToAPI(params: Dictionary<String, Any>, url: URL,
//    authKey: String? = nil, completion: @escaping (_ success: Dictionary<String, Any>) -> Void)
//    {
//        // URL request setup.
//        var request = URLRequest(url: url)
//        request.httpMethod = "POST"
//
//
////        request.httpBody = createBody(parameters: params, boundary: boundary)
//
//
//        request.httpBody = try? JSONSerialization.data(withJSONObject: params, options: [])
//
////        request.setValue("multipart/form-data; boundary=\(boundary)",
////            forHTTPHeaderField: "Content-Type")
//
//        request.setValue("application/json", forHTTPHeaderField: "Content-Type")
//        request.addValue("application/json",forHTTPHeaderField: "Accept")
//
//        // If requesting with autherization key.
//        if let autherizatnKey = authKey {
//            request.addValue(autherizatnKey, forHTTPHeaderField: "Authorization-Key")
//        }
//
//        let session = URLSession.shared
//        let task = session.dataTask(with: request, completionHandler: { data, response, error ->
//            Void in
//            do {
//                let json = try JSONSerialization.jsonObject(with: data!) as! Dictionary<String,
//                    AnyObject>
//                completion(json)
//            } catch {
//                print("Error \(error)")
//            }
//        })
//        task.resume()
//    }
    
    /// Method used to request from API.
    /// - Parameters:
    ///     - params: Add key-value pair of dictionary.
    ///     - url: API URL to request.
    ///     - authKey: (Optional) add if request from autherized user.
    ///     - completion: Closure to handle the response.
    static func requestToAPI(params: Dictionary<String, Any>, url: URL, authKey: String? = nil,
        completion: @escaping (_ success: Dictionary<String, Any>) -> Void) {
        
        var headers: HTTPHeaders =  [:]
        if let autherizatnKey = authKey {
            headers = ["Authorization-Key":autherizatnKey]
        }
        
        AF.upload(multipartFormData: { multipartFormData in
            for (key, value) in params {
                if let arrayJSON = value as? Array<Dictionary<String,Any>> {
                    // Case comes in add/edit task time range
                    var strValue = "["
                    for dictObj in arrayJSON {
                        strValue += "{"
                        for (key, value) in dictObj {
                            strValue += """
"\(key)":"\(value)",
"""
                        }
                        strValue = String(strValue.dropLast())
                        strValue += "},"
                    }
                    // Remove last comma if array object exist.
                    if arrayJSON.count > 0 {
                        strValue = String(strValue.dropLast())
                    }
                    strValue += "]"
                    multipartFormData.append(Data(strValue.utf8), withName: key)
                }
                else if let arrayValues = value as? Array<String> {
                    // This case appears in 'time_range' in create_edit task.
                    var strValue = "["
                    for value in arrayValues {
                        strValue += "\(value),"
                    }
                    strValue = String(strValue.dropLast())
                    strValue += "]"
                    multipartFormData.append(Data(strValue.utf8), withName: key)
                }
                else {
                    multipartFormData.append(Data((value as! String).utf8), withName: key)
                }
            }
        }, to: url, headers: headers)
            .responseJSON { response in
                switch response.result {
                    case .success:
                        print("Validation Successful")
                        let json = self.parseData(JSONData: response.data!)!
                        completion(json)
                    case let .failure(error):
                        print("Failed to update\(error)")
                        let dictError = ["success": 0, "msg":
                            "Error response"] as [String : Any]
                        completion(dictError)
                }
        }
    }
    
    static func parseData(JSONData: Data) -> [String: Any]? {
        do {
            let readableJSON = try JSONSerialization.jsonObject(with: JSONData,
                options:.allowFragments) as! [String: Any]
            return readableJSON
        }
        catch {
            print(error)
            return nil
        }
    }
    
    static func requestMACAddressList() -> Array<String> {
        let arrMAC = ["1c:5f:2b:4c:d9:cf","b8:c1:a2:3d:c3:c"]
        return arrMAC
    }
    
    public static func isConnectedToNetwork() -> Bool {
        var zeroAddress = sockaddr_in()
        zeroAddress.sin_len = UInt8(MemoryLayout<sockaddr_in>.size)
        zeroAddress.sin_family = sa_family_t(AF_INET)

        guard let defaultRouteReachability = withUnsafePointer(to: &zeroAddress, {
            $0.withMemoryRebound(to: sockaddr.self, capacity: 1) {
                SCNetworkReachabilityCreateWithAddress(nil, $0)
            }
        }) else {
            return false
        }

        var flags: SCNetworkReachabilityFlags = []
        if !SCNetworkReachabilityGetFlags(defaultRouteReachability, &flags) {
            return false
        }
        if flags.isEmpty {
            return false
        }

        let isReachable = flags.contains(.reachable)
        let needsConnection = flags.contains(.connectionRequired)

        return (isReachable && !needsConnection)
    }
}

func createBody(parameters: [String: Any],
                boundary: String) -> Data {
    let body = NSMutableData()
    
    let boundaryPrefix = "--\(boundary)\r\n"
    
    for (key, value) in parameters {
        body.appendString(boundaryPrefix)
        body.appendString("Content-Disposition: form-data; name=\"\(key)\"\r\n\r\n")
        body.appendString("\(value)\r\n")
    }
    
    body.appendString(boundaryPrefix)
    body.appendString("\r\n")
    body.appendString("--".appending(boundary.appending("--")))
    
    return body as Data
}

extension NSMutableData {
    func appendString(_ string: String) {
        let data = string.data(using: String.Encoding.utf8, allowLossyConversion: false)
        append(data!)
    }
}
