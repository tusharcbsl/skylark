<?php
require_once './application/config/database.php';
//echo $dbName;
$dbName = $_GET['db'];
//$_SESSION['CLIENTDB'] = "DMS_solar_1664391939";
//$tstRun = "Use " . $_SESSION['CLIENTDB'] . ";";
$tstRun = "Use " . $dbName . ";";
$logout = "CREATE PROCEDURE  `logout`(in userId int,in logoutDate datetime)
BEGIN
	declare logID int;
	update tbl_user_master set current_login_status='0', last_active_logout=logoutDate where user_id=userId;
    select id into logID from tbl_ezeefile_logs where id in(select max(id) from tbl_ezeefile_logs where user_id = userId and action_name = 'Login/Logout');
    if(logID!='') then
		update tbl_ezeefile_logs set end_date=logoutDate where id=logID;
    end if;
    
END";
$commonInAllPages = "
CREATE  PROCEDURE  `commoninallpages`(IN userID int,IN lang varchar(50))
BEGIN
	declare profilePic longtext;
	declare langCode,user_role varchar(50);
    declare errDesc varchar(50) default 0;
	declare role_id, dashboard_mydms, dashboard_mytask, dashboard_edit_profile, dashboard_query, create_user, modify_userlist, delete_userlist, view_userlist, storage_auth_plcy, add_group, delete_group, modify_group, view_group_list, role_add, role_delete, role_modi, role_view, bulk_upload, add_metadata, view_metadata, assign_metadata, create_storage, create_child_storage, upload_doc_storage, modify_storage_level, delete_storage_level, move_storage_level, copy_storage_level, view_user_audit, view_storage_audit, create_workflow, view_workflow_list, edit_workflow, delete_workflow, workflow_step, workflow_initiate_file, workflow_task_track, metadata_search, metadata_quick_search, file_view, file_edit, file_delete, file_anot, file_coment, file_anot_delete, initiate_file, num_of_folder, num_of_file, memory_used, pdf_file, doc_file, excel_file, image_file, audio_file, video_file, pdf_print, pdf_download, pdf_annotation, file_version, delete_version, update_file, workflow_audit, email_config, online_user, tif_file, view_faq, add_faq, edit_faq, del_faq, view_recycle_bin, restore_file, permanent_del, shared_file, share_with_me, export_csv, move_file, copy_file, share_file, checkin_checkout, bulk_download, involve_workflow, run_workflow, feedback_msg, mail_lists, xls_download, xls_print, view_report, add_report, delete_report, update_report, holiday, create_client, user_graph, ezeescan, export_user, import_user, user_activate_deactivate, meta_modify, meta_delete, delete_page, add_page_inbtwn, add_page_no, wf_log, review_log, review_intray, review_track, todo_add, todo_edit, todo_archive, todo_view, appoint_add, appoint_edit, appoint_archive, appoint_view, customize_label, word_edit, delete_metadata, edit_metadata, view_psd, view_cdr, app_default, hindi, english, delete_user_log, delete_storage_log, priority_wf, status_wf, calendar_wf, delete_wf_log, add_holiday, edit_holiday, view_holiday, delete_holiday, holiday_calender, save_query, mail_files, share_folder, shared_folder_with_me, upload_logs, view_rtf, view_odt, password_policy, default_lang_setting, doc_exp_setting, doc_retention_setting, doc_share_setting, lock_folder, lock_file, file_review, doc_weeding_out, doc_share_time, doc_expiry_time, arabic, punjabi, russian, sanskrit, tamil, marathi, folder_upload, rename_file, view_exten, add_exten, enable_exten, delete_exten, login_otp, login_captcha, mis_upload_download_report, view_apikey, add_apikey, regenerate_apikey, delete_apikey, splitpdf, advance_search, view_ocr_list, subscribe_document, view_ppt_pptx, mis_report, view_recycle_storage, restore_storage, delete_storage, rename_storage, view_csv, email_credential, add_email_credential, edit_email_credential, export_ocr, upload_files, export_user_perm tinyint default 0;
	declare docshare_enable_disable,exp_feature_enable,retention_feature_enable,feature_enable_disable,loginotp_enable_disable tinyint default 0;

    	select lang_code from tbl_language where lang_name=lang into langCode;
        SELECT tbl_default_docshare_setting.docshare_enable_disable FROM `tbl_default_docshare_setting` into docshare_enable_disable;
        SELECT tbl_expiry_default_setting.exp_feature_enable FROM `tbl_expiry_default_setting` into exp_feature_enable;
    	SELECT tbl_retention_default_setting.retention_feature_enable FROM `tbl_retention_default_setting` into retention_feature_enable;
    	SELECT tpp.feature_enable_disable,tpp.loginotp_enable_disable FROM `tbl_pass_policy` tpp into feature_enable_disable,loginotp_enable_disable;
        select tur.role_id, tur.user_role, tur.dashboard_mydms, tur.dashboard_mytask, tur.dashboard_edit_profile, tur.dashboard_query, tur.create_user, tur.modify_userlist, tur.delete_userlist, tur.view_userlist, tur.storage_auth_plcy, tur.add_group, tur.delete_group, tur.modify_group, tur.view_group_list, tur.role_add, tur.role_delete, tur.role_modi, tur.role_view, tur.bulk_upload, tur.add_metadata, tur.view_metadata, tur.assign_metadata, tur.create_storage, tur.create_child_storage, tur.upload_doc_storage, tur.modify_storage_level, tur.delete_storage_level, tur.move_storage_level, tur.copy_storage_level, tur.view_user_audit, tur.view_storage_audit, tur.create_workflow, tur.view_workflow_list, tur.edit_workflow, tur.delete_workflow, tur.workflow_step, tur.workflow_initiate_file, tur.workflow_task_track, tur.metadata_search, tur.metadata_quick_search, tur.file_view, tur.file_edit, tur.file_delete, tur.file_anot, tur.file_coment, tur.file_anot_delete, tur.initiate_file, tur.num_of_folder, tur.num_of_file, tur.memory_used, tur.pdf_file, tur.doc_file, tur.excel_file, tur.image_file, tur.audio_file, tur.video_file, tur.pdf_print, tur.pdf_download, tur.pdf_annotation, tur.file_version, tur.delete_version, tur.update_file, tur.workflow_audit, tur.email_config, tur.online_user, tur.tif_file, tur.view_faq, tur.add_faq, tur.edit_faq, tur.del_faq, tur.view_recycle_bin, tur.restore_file, tur.permanent_del, tur.shared_file, tur.share_with_me, tur.export_csv, tur.move_file, tur.copy_file, tur.share_file, tur.checkin_checkout, tur.bulk_download, tur.involve_workflow, tur.run_workflow, tur.feedback_msg, tur.mail_lists, tur.xls_download, tur.xls_print, tur.view_report, tur.add_report, tur.delete_report, tur.update_report, tur.holiday, tur.create_client, tur.user_graph, tur.ezeescan, tur.export_user, tur.import_user, tur.user_activate_deactivate, tur.meta_modify, tur.meta_delete, tur.delete_page, tur.add_page_inbtwn, tur.add_page_no, tur.wf_log, tur.review_log, tur.review_intray, tur.review_track, tur.todo_add, tur.todo_edit, tur.todo_archive, tur.todo_view, tur.appoint_add, tur.appoint_edit, tur.appoint_archive, tur.appoint_view, tur.customize_label, tur.word_edit, tur.delete_metadata, tur.edit_metadata, tur.view_psd, tur.view_cdr, tur.app_default, tur.hindi, tur.english, tur.delete_user_log, tur.delete_storage_log, tur.priority_wf, tur.status_wf, tur.calendar_wf, tur.delete_wf_log, tur.add_holiday, tur.edit_holiday, tur.view_holiday, tur.delete_holiday, tur.holiday_calender, tur.save_query, tur.mail_files, tur.share_folder, tur.shared_folder_with_me, tur.upload_logs, tur.view_rtf, tur.view_odt, tur.password_policy, tur.default_lang_setting, tur.doc_exp_setting, tur.doc_retention_setting, tur.doc_share_setting, tur.lock_folder, tur.lock_file, tur.file_review, tur.doc_weeding_out, tur.doc_share_time, tur.doc_expiry_time, tur.arabic, tur.punjabi, tur.russian, tur.sanskrit, tur.tamil, tur.marathi, tur.folder_upload, tur.rename_file, tur.view_exten, tur.add_exten, tur.enable_exten, tur.delete_exten, tur.login_otp, tur.login_captcha, tur.mis_upload_download_report, tur.view_apikey, tur.add_apikey, tur.regenerate_apikey, tur.delete_apikey, tur.splitpdf, tur.advance_search, tur.view_ocr_list, tur.subscribe_document, tur.view_ppt_pptx, tur.mis_report, tur.view_recycle_storage, tur.restore_storage, tur.delete_storage, tur.rename_storage, tur.view_csv, tur.email_credential, tur.add_email_credential, tur.edit_email_credential, tur.export_ocr, tur.upload_files, tur.export_user_perm
	from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET(userID, user_ids) > 0
	into role_id, user_role, dashboard_mydms, dashboard_mytask, dashboard_edit_profile, dashboard_query, create_user, modify_userlist, delete_userlist, view_userlist, storage_auth_plcy, add_group, delete_group, modify_group, view_group_list, role_add, role_delete, role_modi, role_view, bulk_upload, add_metadata, view_metadata, assign_metadata, create_storage, create_child_storage, upload_doc_storage, modify_storage_level, delete_storage_level, move_storage_level, copy_storage_level, view_user_audit, view_storage_audit, create_workflow, view_workflow_list, edit_workflow, delete_workflow, workflow_step, workflow_initiate_file, workflow_task_track, metadata_search, metadata_quick_search, file_view, file_edit, file_delete, file_anot, file_coment, file_anot_delete, initiate_file, num_of_folder, num_of_file, memory_used, pdf_file, doc_file, excel_file, image_file, audio_file, video_file, pdf_print, pdf_download, pdf_annotation, file_version, delete_version, update_file, workflow_audit, email_config, online_user, tif_file, view_faq, add_faq, edit_faq, del_faq, view_recycle_bin, restore_file, permanent_del, shared_file, share_with_me, export_csv, move_file, copy_file, share_file, checkin_checkout, bulk_download, involve_workflow, run_workflow, feedback_msg, mail_lists, xls_download, xls_print, view_report, add_report, delete_report, update_report, holiday, create_client, user_graph, ezeescan, export_user, import_user, user_activate_deactivate, meta_modify, meta_delete, delete_page, add_page_inbtwn, add_page_no, wf_log, review_log, review_intray, review_track, todo_add, todo_edit, todo_archive, todo_view, appoint_add, appoint_edit, appoint_archive, appoint_view, customize_label, word_edit, delete_metadata, edit_metadata, view_psd, view_cdr, app_default, hindi, english, delete_user_log, delete_storage_log, priority_wf, status_wf, calendar_wf, delete_wf_log, add_holiday, edit_holiday, view_holiday, delete_holiday, holiday_calender, save_query, mail_files, share_folder, shared_folder_with_me, upload_logs, view_rtf, view_odt, password_policy, default_lang_setting, doc_exp_setting, doc_retention_setting, doc_share_setting, lock_folder, lock_file, file_review, doc_weeding_out, doc_share_time, doc_expiry_time, arabic, punjabi, russian, sanskrit, tamil, marathi, folder_upload, rename_file, view_exten, add_exten, enable_exten, delete_exten, login_otp, login_captcha, mis_upload_download_report, view_apikey, add_apikey, regenerate_apikey, delete_apikey, splitpdf, advance_search, view_ocr_list, subscribe_document, view_ppt_pptx, mis_report, view_recycle_storage, restore_storage, delete_storage, rename_storage, view_csv, email_credential, add_email_credential, edit_email_credential, export_ocr, upload_files, export_user_perm;
	select LangCode,docshare_enable_disable,exp_feature_enable,retention_feature_enable,feature_enable_disable,loginotp_enable_disable, role_id, user_role, dashboard_mydms, dashboard_mytask, dashboard_edit_profile, dashboard_query, create_user, modify_userlist, delete_userlist, view_userlist, storage_auth_plcy, add_group, delete_group, modify_group, view_group_list, role_add, role_delete, role_modi, role_view, bulk_upload, add_metadata, view_metadata, assign_metadata, create_storage, create_child_storage, upload_doc_storage, modify_storage_level, delete_storage_level, move_storage_level, copy_storage_level, view_user_audit, view_storage_audit, create_workflow, view_workflow_list, edit_workflow, delete_workflow, workflow_step, workflow_initiate_file, workflow_task_track, metadata_search, metadata_quick_search, file_view, file_edit, file_delete, file_anot, file_coment, file_anot_delete, initiate_file, num_of_folder, num_of_file, memory_used, pdf_file, doc_file, excel_file, image_file, audio_file, video_file, pdf_print, pdf_download, pdf_annotation, file_version, delete_version, update_file, workflow_audit, email_config, online_user, tif_file, view_faq, add_faq, edit_faq, del_faq, view_recycle_bin, restore_file, permanent_del, shared_file, share_with_me, export_csv, move_file, copy_file, share_file, checkin_checkout, bulk_download, involve_workflow, run_workflow, feedback_msg, mail_lists, xls_download, xls_print, view_report, add_report, delete_report, update_report, holiday, create_client, user_graph, ezeescan, export_user, import_user, user_activate_deactivate, meta_modify, meta_delete, delete_page, add_page_inbtwn, add_page_no, wf_log, review_log, review_intray, review_track, todo_add, todo_edit, todo_archive, todo_view, appoint_add, appoint_edit, appoint_archive, appoint_view, customize_label, word_edit, delete_metadata, edit_metadata, view_psd, view_cdr, app_default, hindi, english, delete_user_log, delete_storage_log, priority_wf, status_wf, calendar_wf, delete_wf_log, add_holiday, edit_holiday, view_holiday, delete_holiday, holiday_calender, save_query, mail_files, share_folder, shared_folder_with_me, upload_logs, view_rtf, view_odt, password_policy, default_lang_setting, doc_exp_setting, doc_retention_setting, doc_share_setting, lock_folder, lock_file, file_review, doc_weeding_out, doc_share_time, doc_expiry_time, arabic, punjabi, russian, sanskrit, tamil, marathi, folder_upload, rename_file, view_exten, add_exten, enable_exten, delete_exten, login_otp, login_captcha, mis_upload_download_report, view_apikey, add_apikey, regenerate_apikey, delete_apikey, splitpdf, advance_search, view_ocr_list, subscribe_document, view_ppt_pptx, mis_report, view_recycle_storage, restore_storage, delete_storage, rename_storage, view_csv, email_credential, add_email_credential, edit_email_credential, export_ocr, upload_files, export_user_perm;

END";

$insertData = "CREATE  PROCEDURE `insert_data`(
IN in_table_name varchar(355),
IN in_column_name longtext,
IN in_column_values longtext
)
BEGIN

	SET @q1 = CONCAT('INSERT INTO ' , in_table_name , ' (' , in_column_name , ' ) VALUES ( ' , in_column_values , ' ) ' );
       
    PREPARE stmt3 FROM @q1;
	EXECUTE stmt3;
	DEALLOCATE PREPARE stmt3;
END
";
$selectData = "CREATE  PROCEDURE  `select_data`(IN in_table_name VARCHAR(100)

,IN in_selected_col varchar(500)

,IN in_condition varchar(1000)

,IN join_condition varchar(1000)

)
BEGIN



if join_condition != '' && in_condition != '' then

	SET @t1 = CONCAT('SELECT ', in_selected_col ,' FROM ', in_table_name, ' join ' , join_condition ,' WHERE ', in_condition );

elseif join_condition != '' && in_condition = '' then 

	SET @t1 = CONCAT('SELECT ', in_selected_col ,' FROM ', in_table_name, ' join ' , join_condition );

elseif join_condition = '' && in_condition !='' then

	SET @t1 = CONCAT('SELECT ', in_selected_col ,' FROM ', in_table_name, ' WHERE ', in_condition );

else

	SET @t1 = CONCAT('SELECT ', in_selected_col ,' FROM ', in_table_name );

end if;



PREPARE stmt3 FROM @t1;

EXECUTE stmt3;

DEALLOCATE PREPARE stmt3;



END
";

$spLogin = "CREATE PROCEDURE  `sp_login`(IN emailID varchar(50), IN pwd varchar(200), in pwdDays int)
BEGIN

    declare intCount int;

    declare errFlag int;

    declare userActive int;

    declare pwdDate datetime;

    declare failedAttempts int;

    declare pwdValidation int;

    declare currLogin int;

    declare userID int;

    declare allowedUser,userCount int default 0;

    set intCount=0;

    set errFlag = 0;

    set failedAttempts = 0;

    set userActive = 0;

    set pwdValidation=0;

    set currLogin=0;

    select noofuser_allow_login into allowedUser from tbl_pass_policy;

    SELECT count(user_id) into userCount FROM tbl_user_master WHERE current_login_status='1' and user_id!='1';

    if allowedUser=0 or allowedUser>userCount then

		select count(*),failed_login_attempts,active_inactive_users into intCount,failedAttempts,userActive from tbl_user_master where (user_email_id=emailID or emp_id=emailID);

		if intCount>0 then

			#select 'User Exists' Message;

			set intCount = 0;

			select count(*),user_id, active_inactive_users, last_pass_change,current_login_status from tbl_user_master where (user_email_id=emailID or emp_id=emailID) and password=sha1(pwd) into intCount, userID,userActive, pwdDate,currLogin;

			if intCount>0 then

				#select 'Valid Username and Password' Message;

				set intCount=0;

				if userActive=1 then

					#select 'User Active' Message;

					if currLogin=0 then

						if failedAttempts<=5 then

							#select 'User Attempt Valid' Message;

							select edate into pwdValidation from tbl_pass_policy;

							if pwdValidation>0 then

								if datediff(now(),pwdDate)<=pwdDays then

									#select 'Password Days Valid' Message;

									set errFlag=0;

									set failedAttempts = 0;

									update tbl_user_master set failed_login_attempts=failedAttempts where (user_email_id=emailID or emp_id=emailID);

								else

									#password validity end

									set errFlag = 6;

								end if;

							else

								#password validity end

								set errFlag=0;

								set failedAttempts = 0;

								update tbl_user_master set failed_login_attempts=failedAttempts where (user_email_id=emailID or emp_id=emailID);

							end if;

						else

							#failed attempt more than 5

							update tbl_user_master set active_inactive_users=0 where (user_email_id=emailID or emp_id=emailID) and password=sha1(pwd);

							set errFlag = 3;

						end if;

					else

						set errFlag=1;

						#'User already logged in';

					end if;

				else

					#user inactive

					if failedAttempts<5 then

						set errFlag = 4;

					else

						set errFlag = 3;

					end if;

				end if;

			else

				#invalid Username and Password

				

				if(failedAttempts is null) then 

					set failedAttempts = 1;

				 else

					set failedAttempts = failedAttempts + 1;

				end if;

				

				if failedAttempts<5 then

					update tbl_user_master set failed_login_attempts=failedAttempts where (user_email_id=emailID or emp_id=emailID);

					set errFlag = 2;

				else

					update tbl_user_master set active_inactive_users=0 where  (user_email_id=emailID or emp_id=emailID);

					set errFlag = 3;

				end if;

			end if;

		else

			#user not exist

			set errFlag = 5;

			

		end if;

	else

		set errFlag = 7;

	end if;

    if errFlag=0 || errFlag=6 then

        select errFlag,user_id, first_name, middle_name, last_name,user_email_id,designation,last_active_login,lang,phone_no,current_login_status from tbl_user_master where user_id=userID;

    else

		select errFlag , failedAttempts;

	end if;

END
";

$spUserStatus = "
CREATE PROCEDURE `sp_UserStatus`(in userID int, in sysIP varchar(45))
BEGIN
	Declare current_login_status int;
	Declare active_inactive_users int;
	Declare errFlag int;
	Declare logid int;
    declare lang varchar(20);
	Declare userName varchar(200) charset utf8;
	declare last_active_logout varchar(20);
	set current_login_status=0;
	set active_inactive_users=0;
	set errFlag = 0;
	set logid=0;
	select tum.current_login_status, tum.active_inactive_users, concat(first_name,' ',last_name),tum.last_active_logout,tum.lang from tbl_user_master tum where user_id=userID into current_login_status, active_inactive_users, userName,last_active_logout,lang;
	
	if current_login_status=1 and active_inactive_users=0 then
		set errFlag = 1;
		select max(id) from tbl_ezeefile_logs where user_id=userID and action_name='Login/Logout' into logid;
		update tbl_ezeefile_logs set end_date=now() where id=logid;
		insert into tbl_ezeefile_logs (user_id, user_name, action_name, start_date, end_date, system_ip, remarks) values (userID, userName, 'Login/Logout', now(), now(), sysIP, 'User Deactivated By System Administrator');
		update tbl_user_master set current_login_status=0,last_active_logout=now() where user_id=userID;
	end if;

	if errFlag=1 then
		select errFlag;
    else
		select last_active_logout,current_login_status,active_inactive_users,userName,lang,errFlag;
	end if;
END
";

$userWfList = "
CREATE PROCEDURE `userWorflowList`(IN userId int,IN lType tinyint)
BEGIN
	if lType =false then 
		select twm.* from tbl_workflow_master twm inner join tbl_workflow_to_group twg on twm.workflow_id=twg.workflow_id inner join tbl_bridge_grp_to_um tbgu on find_in_set(tbgu.group_id,twg.group_id) where  find_in_set(UserId,tbgu.user_ids) group by twm.workflow_id;
	else
		select rp_id,wf_id,report_name from tbl_wf_reports twr join tbl_bridge_grp_to_report tbgr on twr.rp_id=tbgr.report_id inner join tbl_bridge_grp_to_um tbgu on tbgr.group_id=tbgu.group_id join tbl_workflow_to_group twg on find_in_set(tbgu.group_id,twg.group_id) where find_in_set(userId,user_ids) group by rp_id;
	end if;
END
";

$wfstsDashBord = "
CREATE PROCEDURE `workflowStatusDashboard`(in userId int,in ws varchar(50))
BEGIN

	declare pending int default 0;

    declare processed int default 0;

    declare complete int default 0;

    declare approved int default 0;

    declare urgent int default 0;

    declare Mediuma int default 0;

    declare normal int default 0;

    if ws='status'  then

		if userId='' then

			SELECT count(id) FROM tbl_doc_assigned_wf tdawf where tdawf.task_status = 'Pending' into pending;

			SELECT count(id) FROM tbl_doc_assigned_wf tdawf where tdawf.task_status = 'Processed' into processed;

			SELECT count(id) FROM tbl_doc_assigned_wf tdawf where tdawf.task_status = 'Complete' or tdawf.task_status = 'Approved' or tdawf.task_status = 'Done' into complete;

            

            SELECT count(id) into urgent FROM tbl_task_master tsm inner join tbl_doc_assigned_wf tdawf on tsm.task_id=tdawf.task_id where tsm.priority_id = 1;

            SELECT count(id) into Mediuma FROM tbl_task_master tsm inner join tbl_doc_assigned_wf tdawf on tsm.task_id=tdawf.task_id where tsm.priority_id = 2;

            SELECT count(id) into normal FROM tbl_task_master tsm inner join tbl_doc_assigned_wf tdawf on tsm.task_id=tdawf.task_id where tsm.priority_id = 3;

		else

			SELECT count(id) into pending FROM tbl_task_master tsm inner join tbl_doc_assigned_wf tdawf on tsm.task_id=tdawf.task_id where ((tsm.assign_user='userId' and tdawf.NextTask='0') or (alternate_user='userId' and tdawf.NextTask= '3') or (supervisor='userId' and tdawf.NextTask= '4')) and tdawf.task_status = 'Pending';

			SELECT count(id) into processed FROM tbl_task_master tsm inner join tbl_doc_assigned_wf tdawf on tsm.task_id=tdawf.task_id where action_by='userId' and tdawf.task_status = 'Processed';

			SELECT count(id) into complete FROM tbl_task_master tsm inner join tbl_doc_assigned_wf tdawf on tsm.task_id=tdawf.task_id where action_by='userId' and tdawf.task_status = 'Complete' or tdawf.task_status = 'Approved' or tdawf.task_status = 'Done';

            

            SELECT count(id) into urgent FROM tbl_task_master tsm inner join tbl_doc_assigned_wf tdawf on tsm.task_id=tdawf.task_id where tsm.priority_id = 1 and tsm.assign_user='userId';

            SELECT count(id) into Mediuma FROM tbl_task_master tsm inner join tbl_doc_assigned_wf tdawf on tsm.task_id=tdawf.task_id where tsm.priority_id = 2 and tsm.assign_user='userId';

            SELECT count(id) into normal FROM tbl_task_master tsm inner join tbl_doc_assigned_wf tdawf on tsm.task_id=tdawf.task_id where tsm.priority_id = 3 and tsm.assign_user='userId';

		end if;		

    end if;

    select pending,processed,complete,urgent,Mediuma,normal;

END
";

$run = mysqli_query($db_con, $tstRun) or die(mysqli_error($db_con));
$sql = mysqli_query($db_con, $commonInAllPages) or die(mysqli_error($db_con));
$sql = mysqli_query($db_con, $logout) or die(mysqli_error($db_con));
$sql = mysqli_query($db_con, $insertData) or die(mysqli_error($db_con));
$sql = mysqli_query($db_con, $selectData) or die(mysqli_error($db_con));
$sql = mysqli_query($db_con, $spLogin) or die(mysqli_error($db_con));
$sql = mysqli_query($db_con, $spUserStatus) or die(mysqli_error($db_con));
$sql = mysqli_query($db_con, $userWfList) or die(mysqli_error($db_con));
$sql = mysqli_query($db_con, $wfstsDashBord) or die(mysqli_error($db_con));

if ($sql) {
	echo "PROCEDURE CREATED SUCCESSFULLY.";
	echo '<a href="index">CLICK HERE FOR DASHBOARD</a>';
}else{
	echo "PROCEDURE CREATION FAILED.";
	//echo '<a href="procedure">CLICK HERE FOR CREATE PROCEDURE</a>';
}
