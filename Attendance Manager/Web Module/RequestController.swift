//
//  RequestController.swift
//  Attendance Manager
//
//  Created by Sachin on 9/27/19.
//  Copyright © 2019 Sachin. All rights reserved.
//

import Foundation
import UIKit
import SystemConfiguration

class RequestController: NSObject {
    
    func requestLogin(params: Dictionary<String, String>, url: String, completion: @escaping
        (_ success: Dictionary<String, Any>) -> Void) {
        var request = URLRequest(url: URL(string:
            url)!)
        request.httpMethod = "POST"
        request.httpBody = try? JSONSerialization.data(withJSONObject: params, options: [])
        request.addValue("application/json", forHTTPHeaderField: "Content-Type")
        
        let session = URLSession.shared
        let task = session.dataTask(with: request, completionHandler: { data, response, error ->
            Void in
            do {
                let json = try JSONSerialization.jsonObject(with: data!) as! Dictionary<String,
                    AnyObject>
                completion(json)
            } catch {
                print("error")
            }
        })
        task.resume()
    }
    
    func requestMACAddressList() -> Array<String> {
        let arrMAC = ["1c:5f:2b:4c:d9:cf","b8:c1:a2:3d:c3:c"]
        return arrMAC
    }
    
    public func isConnectedToNetwork() -> Bool {
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
