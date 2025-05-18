
import React, { useState, useEffect, useCallback } from "react";
import { DragDropContext, Droppable, Draggable } from "react-beautiful-dnd";
import { Button } from "@/components/ui/button";
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
import { Switch } from "@/components/ui/switch";
import { Label } from "@/components/ui/label";
import { Card, CardContent } from "@/components/ui/card";
import { toast } from "@/components/ui/sonner";
import {
  Tabs,
  TabsContent,
  TabsList,
  TabsTrigger,
} from "@/components/ui/tabs";
import { Settings, Save, Menu } from "lucide-react";
import Header from "@/components/Header";
import PostItem from "@/components/PostItem";
import SettingsPanel from "@/components/SettingsPanel";
import { mockPosts, mockCategories } from "@/data/mockData";

const Index = () => {
  const [posts, setPosts] = useState([]);
  const [selectedCategory, setSelectedCategory] = useState("all");
  const [isAutoSave, setIsAutoSave] = useState(true);
  const [activeTab, setActiveTab] = useState("posts");

  useEffect(() => {
    // Load posts based on selected category
    const filteredPosts =
      selectedCategory === "all"
        ? mockPosts
        : mockPosts.filter((post) => post.category === selectedCategory);
    setPosts(filteredPosts);
  }, [selectedCategory]);

  const onDragEnd = useCallback((result) => {
    if (!result.destination) return;

    const items = Array.from(posts);
    const [reorderedItem] = items.splice(result.source.index, 1);
    items.splice(result.destination.index, 0, reorderedItem);

    setPosts(items);
    
    if (isAutoSave) {
      saveOrder(items);
    }
  }, [posts, isAutoSave]);

  const saveOrder = (items = posts) => {
    // This would be an API call in a real WordPress plugin
    console.log("Saving order:", items.map(post => post.id));
    toast.success("Post order saved successfully");
  };

  return (
    <div className="min-h-screen bg-gray-50">
      <Header />
      
      <main className="container mx-auto px-4 py-8">
        <div className="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
          <h1 className="text-3xl font-bold">SortSnap</h1>
          <div className="flex flex-col md:flex-row gap-4">
            <div className="w-full md:w-64">
              <Select
                value={selectedCategory}
                onValueChange={setSelectedCategory}
              >
                <SelectTrigger className="w-full">
                  <SelectValue placeholder="Select Category" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="all">All Categories</SelectItem>
                  {mockCategories.map((category) => (
                    <SelectItem key={category.id} value={category.id}>
                      {category.name}
                    </SelectItem>
                  ))}
                </SelectContent>
              </Select>
            </div>
            
            <div className="flex items-center space-x-2">
              <Switch
                id="autosave"
                checked={isAutoSave}
                onCheckedChange={setIsAutoSave}
              />
              <Label htmlFor="autosave">Auto Save</Label>
            </div>
            
            {!isAutoSave && (
              <Button onClick={() => saveOrder()} className="whitespace-nowrap">
                <Save className="mr-2 h-4 w-4" /> Save Order
              </Button>
            )}
          </div>
        </div>

        <Card>
          <Tabs defaultValue="posts" value={activeTab} onValueChange={setActiveTab}>
            <TabsList className="grid w-full grid-cols-2">
              <TabsTrigger value="posts">
                <Menu className="mr-2 h-4 w-4" /> Posts
              </TabsTrigger>
              <TabsTrigger value="settings">
                <Settings className="mr-2 h-4 w-4" /> Settings
              </TabsTrigger>
            </TabsList>
            
            <TabsContent value="posts" className="p-4">
              <DragDropContext onDragEnd={onDragEnd}>
                <Droppable droppableId="posts">
                  {(provided) => (
                    <div
                      {...provided.droppableProps}
                      ref={provided.innerRef}
                      className="space-y-2"
                    >
                      {posts.length > 0 ? (
                        posts.map((post, index) => (
                          <Draggable
                            key={post.id}
                            draggableId={post.id.toString()}
                            index={index}
                          >
                            {(provided, snapshot) => (
                              <PostItem
                                post={post}
                                provided={provided}
                                isDragging={snapshot.isDragging}
                              />
                            )}
                          </Draggable>
                        ))
                      ) : (
                        <div className="text-center py-8 text-gray-500">
                          No posts found in this category
                        </div>
                      )}
                      {provided.placeholder}
                    </div>
                  )}
                </Droppable>
              </DragDropContext>
            </TabsContent>
            
            <TabsContent value="settings">
              <CardContent className="p-6">
                <SettingsPanel />
              </CardContent>
            </TabsContent>
          </Tabs>
        </Card>
      </main>
    </div>
  );
};

export default Index;
