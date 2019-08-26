//
//  AddProjects.swift
//  Attendance Manager
//
//  Created by Sachin on 9/19/19.
//  Copyright © 2019 Sachin. All rights reserved.
//

import Foundation
import UIKit
import CoreData

class AddProjects {
    var nsMOForUserTimes: NSManagedObject!
    var nsManagedContext: NSManagedObjectContext!
    init() {
        guard let appDelegate = UIApplication.shared.delegate as? AppDelegate else { return }
        nsManagedContext = appDelegate.persistentContainer.viewContext
    }
    
    func addNewProject(projectName: String, projectIconUrl: String) {
        let userEntity = NSEntityDescription.entity(forEntityName: "Projects",
                                                    in: nsManagedContext)!
        nsMOForUserTimes = NSManagedObject(entity: userEntity, insertInto: nsManagedContext)
        var count = 0
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Projects")
        do {
            count = try nsManagedContext.count(for: fetchRequest)
        } catch {
            print(error.localizedDescription)
        }
        nsMOForUserTimes.setValuesForKeys(["projectId": count, "projectName": projectName,
                                           "projectIconUrl": projectIconUrl, "totalTime": 0])
        do {
            try nsManagedContext.save()
        } catch let error as NSError {
            print("Could not save. \(error), \(error.userInfo)")
        }
    }
    
    func getProjectId(project name: String) -> Int {
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Projects")
        fetchRequest.predicate = NSPredicate(format: "projectName = %@", name)
        var projId: Int!
        do {
            let test = try nsManagedContext.fetch(fetchRequest)
            let objectUpdate = test[0] as! NSManagedObject
            projId = objectUpdate.value(forKey: "projectId") as? Int
        }
        catch {
            print("Error")
        }
        return projId
    }
    
    func getProjectIconUrl(projectId: Int) -> URL {
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Projects")
        fetchRequest.predicate = NSPredicate(format: "projectId = %d", projectId)
        var projUrl: URL!
        do {
            let test = try nsManagedContext.fetch(fetchRequest)
            let objectUpdate = test[0] as! NSManagedObject
            let url = objectUpdate.value(forKey: "projectIconUrl") as! String
            projUrl = URL(string: url)
        }
        catch {
            print("Error")
        }
        return projUrl
    }
    
    func getProjectName(projId: Int) -> String {
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Projects")
        fetchRequest.predicate = NSPredicate(format: "projectId = %d", projId)
        
        var projName: String!
        do {
            let test = try nsManagedContext.fetch(fetchRequest)
            let objectUpdate = test[0] as! NSManagedObject
            projName = objectUpdate.value(forKey: "projectName") as? String
        }
        catch {
            print("Error")
        }
        return projName
    }
    
    func isProjectExist(projectName: String) -> Bool {
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Projects")
        fetchRequest.predicate = NSPredicate(format: "projectName == %@", projectName)
        
        let res = try! nsManagedContext.fetch(fetchRequest)
        return res.count > 0 ? true : false
    }
    
    func fetchAllData() {
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Projects")
        fetchRequest.sortDescriptors = [NSSortDescriptor(key: "projectId", ascending: false)]
        do {
            let result = try nsManagedContext.fetch(fetchRequest)
            print(result.count)
            for data in result as! [NSManagedObject] {
                print("Project Id: \(data.value(forKey: "projectId") as! Int)")
                print("Project Name: \(data.value(forKey: "projectName") as! String)")
                print("Project Icon Url: \(data.value(forKey: "projectIconUrl") as! String)")
                print("Total Time: \(data.value(forKey: "totalTime") as! Int)")
            }
        } catch {
            print("Failed")
        }
    }
    
    func getAllProjectNames() -> Array<String> {
        var arrNames = Array<String>()
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Projects")
        fetchRequest.sortDescriptors = [NSSortDescriptor(key: "projectId", ascending: true)]
        do {
            let result = try nsManagedContext.fetch(fetchRequest)
            print(result.count)
            for data in result as! [NSManagedObject] {
                arrNames.append(data.value(forKey: "projectName") as! String)
            }
        } catch {
            print("Failed")
        }
        return arrNames
    }
    
    func getProjectNameAndIconUrl() -> Dictionary<String, URL> {
        var dictProjNameUrl = Dictionary<String, URL>()
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Projects")
        do {
            let result = try nsManagedContext.fetch(fetchRequest)
            for data in result as! [NSManagedObject] {
                let strUrl = data.value(forKey: "projectIconUrl") as! String
                let url = URL(string: strUrl)
                let name = data.value(forKey: "projectName") as! String
                dictProjNameUrl.updateValue(url!, forKey: name)
            }
        } catch {
            print("Failed")
        }
        return dictProjNameUrl
    }
}
