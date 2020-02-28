 /*//////////////////////////////////////////////////////////////////////////////
 //
 //    Copyright (c) GreenPrint Technologies LLC. 2019
 //
 //    File Name         : ProjectsCDController.swift
 //
 //    File Created      : 19:Sept:2019
 //
 //    Dev Name          : Sachin Kumar K.
 //
 //    Description       : Projects entity Core data handler..
 //
 //////////////////////////////////////////////////////////////////////////// */

import Foundation
import UIKit
import CoreData

class ProjectsCDController {
    var nsMOForUserTimes: NSManagedObject!
    var nsManagedContext: NSManagedObjectContext!
    init() {
        guard let appDelegate = UIApplication.shared.delegate as? AppDelegate else { return }
        nsManagedContext = appDelegate.persistentContainer.viewContext
    }
    
    /// Commit database.
    func saveContext() {
        do {
            try nsManagedContext.save()
        } catch let error as NSError {
            print("Could not save. \(error), \(error.userInfo)")
        }
    }
    
    /// Add new projects to core data.
    func addOrNewProject(projId: Int, projectName: String, projectIconUrl: String, projColor:
            String) {
        if !isProjectExist(projectId: projId) {
            let userEntity = NSEntityDescription.entity(forEntityName: "Projects",
                                                        in: nsManagedContext)!
            nsMOForUserTimes = NSManagedObject(entity: userEntity, insertInto: nsManagedContext)
            nsMOForUserTimes.setValuesForKeys(["project_id": projId, "project_name": projectName,
            "project_icon_url": projectIconUrl, "project_color": projColor])
            saveContext()
        }
        else {
            let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Projects")
            fetchRequest.predicate = NSPredicate(format: "project_id = %d", projId)
            let res = try! nsManagedContext.fetch(fetchRequest)
            if res.count > 0 {
                let nsMObject = res[0] as! NSManagedObject
                nsMObject.setValue(projectName, forKey: "project_name")
                nsMObject.setValue(projectIconUrl, forKey: "project_icon_url")
                nsMObject.setValue(projColor, forKey: "project_color")
            }
        }
    }
    
    /// get project name from project id.
    func getProjectId(project name: String) -> Int {
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Projects")
        fetchRequest.predicate = NSPredicate(format: "project_name = %@", name)
        var projId: Int!
        do {
            let nsMContext = try nsManagedContext.fetch(fetchRequest)
            let nsMObject = nsMContext[0] as! NSManagedObject
            projId = nsMObject.value(forKey: "project_id") as? Int
        }
        catch {
            print("Error")
        }
        return projId
    }
    
    /// get project name from project id.
    func getProjectName(projId: Int) -> String {
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Projects")
        fetchRequest.predicate = NSPredicate(format: "project_id = %d", projId)
        var projName: String!
        do {
            let nsMContext = try nsManagedContext.fetch(fetchRequest)
            let nsMObject = nsMContext[0] as! NSManagedObject
            projName = nsMObject.value(forKey: "project_name") as? String
        }
        catch {
            print("Error")
        }
        return projName
    }
    
    /// returns true if project exists.
    func isProjectExist(projectId: Int) -> Bool {
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Projects")
        fetchRequest.predicate = NSPredicate(format: "project_id == %d", projectId)
        let res = try! nsManagedContext.fetch(fetchRequest)
        return res.count > 0 ? true : false
    }
    
    /// Get all details (In dictionary : Key = Project Id, Value = TaskDetails class object).
    func getAllProjectDetails() -> Dictionary<Int, ProjectDetails> {
        var dictProjDetails = Dictionary<Int, ProjectDetails>()
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Projects")
        do {
            let nsMContext = try nsManagedContext.fetch(fetchRequest)
            for nsMObject in nsMContext as! [NSManagedObject] {
                let projId = nsMObject.value(forKey: "project_id") as! Int
                let name = nsMObject.value(forKey: "project_name") as! String
                var url: URL!
                if let dbUrl = URL(string: nsMObject.value(forKey: "project_icon_url") as! String) {
                    url = dbUrl
                }
                else {
                    url = URL(string:
                        "https://www.iconsdb.com/icons/download/guacamole-green/exclamation-32.png")
                }
                var color: UIColor!
                if let uiColor = UIColor(hexString: nsMObject.value(forKey: "project_color")
                    as! String) {
                    color = uiColor
                }
                else {
                    color = .green
                }
                
                // Fetch modules.
                let moduleCDCtrlr = ModuleCDController()
                let dictModules = moduleCDCtrlr.getModuleIdAndNames(projId: projId)
                let cProjectDetails = ProjectDetails(projId: projId, projName: name, url: url!,
                                                     dictMod: dictModules, color: color)
                dictProjDetails.updateValue(cProjectDetails, forKey: projId)
            }
        } catch {
            print("Failed")
        }
        return dictProjDetails
    }
    
    /// Deletes all the data in Projects entiy.
    func deleteAllData() {
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Projects")
        fetchRequest.returnsObjectsAsFaults = false
        do {
            let results = try nsManagedContext.fetch(fetchRequest)
            for object in results {
                guard let objectData = object as? NSManagedObject else {continue}
                nsManagedContext.delete(objectData)
            }
        }
        catch {
            print("Error")
        }
        let moduleCDCtrlr = ModuleCDController()
        moduleCDCtrlr.deleteAllData()
        saveContext()
    }
}
